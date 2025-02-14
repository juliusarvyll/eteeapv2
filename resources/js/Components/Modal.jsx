import { useState, useEffect } from 'react';

export default function Modal({
    show = false,
    maxWidth = '2xl',
    closeable = true,
    onClose = () => {},
    title,
    children,
}) {
    const [isOpen, setIsOpen] = useState(show);

    const maxWidthClass = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[maxWidth];

    useEffect(() => {
        setIsOpen(show);
    }, [show]);

    const close = () => {
        if (closeable) {
            setIsOpen(false);
            onClose();
        }
    };

    return (
        <div 
            className={`fixed z-50 inset-0 overflow-y-auto transition-all duration-300 ${isOpen ? 'visible' : 'invisible'}`}
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div className="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div 
                    onClick={close}
                    className={`fixed inset-0 bg-black bg-opacity-30 transition-opacity ${isOpen ? 'opacity-100' : 'opacity-0'}`}
                    aria-hidden="true"
                ></div>

                <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div className={`inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full ${maxWidthClass} ${isOpen ? 'scale-100 opacity-100' : 'scale-95 opacity-0'}`}>
                    <div className="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div className="flex justify-between items-center mb-4">
                            {title && (
                                <h3 className="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {title}
                                </h3>
                            )}
                            {closeable && (
                                <button
                                    onClick={close}
                                    className="text-gray-400 hover:text-gray-500 focus:outline-none"
                                >
                                    <span className="sr-only">Close</span>
                                    ✕
                                </button>
                            )}
                        </div>
                        <div className="mt-2">
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
