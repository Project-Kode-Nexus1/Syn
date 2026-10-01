import { create } from 'zustand';
import Phone from '@/components/Phone';

const usePhoneStore = create((set) => ({
    locked: true,
    qs: false,
    bios: false,
    toggleQs: () => set((state) => ({ qs: !state.qs })),
    setQs: (v) => set((state) => ({ qs: v })),
    toggleLock: () => set((state) => ({ locked: !state.locked })),
    currentTime: new Date(),
    setCurrentTime: () => set({ currentTime: new Date() }),
    allApps: [
        { id: 1, name: 'Phone', icon: '/phone.png', comp: <Phone /> },
        { id: 2, name: 'Messages', icon: '/messages.png' },
        { id: 3, name: 'Camera', icon: '/camera.webp' },
        { id: 4, name: 'Phone', icon: '/phone.png', comp: <Phone /> },
        { id: 5, name: 'Messages', icon: '/messages.png' },
    ],
    currentApp: null,
    openedApps: [],
    openApp: (app) => set((state) => ({
        currentApp: app,
        openedApps: [...state.openedApps, app],
    })),
    backFromApp: () => set({ currentApp: null }),
    closeApp: (app) => set((state) => ({
        currentApp: state.currentApp === app ? null : state.currentApp,
        openedApps: state.openedApps.filter((a) => a !== app),
    })),
}));


export default usePhoneStore;