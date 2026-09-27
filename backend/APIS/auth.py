import os
from dotenv import load_dotenv
from fastapi import FastAPI, HTTPException, Request, Query, Cookie, Depends
from fastapi.responses import RedirectResponse, JSONResponse
from supabase import create_client, Client
from fastapi.middleware.cors import CORSMiddleware

load_dotenv()
app=FastAPI()
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://127.0.0.1:5173",
        "http://localhost:5173",
    ],
    allow_credentials=True,
    allow_methods=["GET", "POST", "PATCH", "PUT", "DELETE"],
    allow_headers=["Content-Type", "Authorization"],
)
supabase: Client=create_client(os.getenv('supabaseurl'), os.getenv('supabasekey'))

@app.get('/')
async def root(request: Request):
    try:
        return {"status": "OK"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get('/auth/google')
async def googleauth():
    try:
        response=supabase.auth.sign_in_with_oauth({'provider': 'google', 'options': {'redirect_to': 'http://127.0.0.1:8000/auth/callback'}})
        return RedirectResponse(url=response.url, status_code=302)
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/auth/callback")
async def google_callback(code: str = Query(...)):
    try:
        response = supabase.auth.exchange_code_for_session({
            "auth_code": code
        })

        if not response.user or not response.session:
            raise HTTPException(
                status_code=401,
                detail="Failed to create the session"
            )

        redirect = RedirectResponse(
            url=os.getenv('FRONTEND_URL'),
            status_code=302
        )

        redirect.set_cookie(
            key="access_token",
            value=response.session.access_token,
            httponly=True,
            secure=False,  # Production HTTPS: True
            samesite="lax",
            max_age=3600
        )

        return redirect

    except HTTPException:
        raise

    except Exception:
        raise HTTPException(
            status_code=400,
            detail="Invalid OAuth callback code."
        )
    
@app.get("/auth/me")
async def get_me(access_token: str | None = Cookie(default=None)):
    if not access_token:
        raise HTTPException(
            status_code=401,
            detail="THere are no users"
        )

    try:
        response = supabase.auth.get_user(access_token)

        return {
            "id": response.user.id,
            "email": response.user.email,
            "provider": response.user.app_metadata.get("provider")
        }

    except Exception:
        raise HTTPException(
            status_code=401,
            detail="Invalid session."
        )

@app.post("/auth/logout")
async def logout():
    response = JSONResponse({
        "message": "You logged out"
    })

    response.delete_cookie(
        key="access_token"
    )

    return response

async def get_current_user(
    access_token: str | None = Cookie(default=None)
):
    if not access_token:
        raise HTTPException(
            status_code=401,
            detail="You need to log in"
        )

    try:
        response = supabase.auth.get_user(access_token)

        return response.user

    except Exception:
        raise HTTPException(
            status_code=401,
            detail="Invalid token."
        )




