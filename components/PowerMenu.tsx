import React, { useState } from 'react'
import { Button, Dialog, DialogPanel, DialogTitle } from '@headlessui/react'
import { BsBack } from 'react-icons/bs'
import { FaBackward } from 'react-icons/fa'

export default function PowerMenu({ isOpen, close }: { isOpen: boolean, close: () => void }) {
    return (
        <Dialog open={isOpen} as="div" className="relative z-80 focus:outline-none w-full" onClose={close}>
            <div className="fixed inset-0 z-80 overflow-y-auto flex justify-center items-center">
                <DialogPanel
                    transition
                    className="w-40 rounded-xl bg-white/5 flex flex-col gap-4 items-center justify-center p-4 backdrop-blur-2xl duration-300 ease-out data-closed:transform-[scale(95%)] data-closed:opacity-0 translate-[25vw - 160px]"
                >
                    <Button
                        className="inline-flex items-center gap-2 rounded-md bg-gray-700 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-inner shadow-white/10 focus:not-data-focus:outline-none data-focus:outline data-focus:outline-white data-hover:bg-gray-600 data-open:bg-gray-700 cursor-pointer"
                        onClick={close}
                    >
                        Enter BIOS
                    </Button>
                    <Button
                        className="inline-flex items-center gap-2 rounded-md bg-gray-700 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-inner shadow-white/10 focus:not-data-focus:outline-none data-focus:outline data-focus:outline-white data-hover:bg-gray-600 data-open:bg-gray-700 cursor-pointer"
                        onClick={close}
                    >
                        <FaBackward />
                        Go Back
                    </Button>
                </DialogPanel>
            </div>
        </Dialog>
    )
}