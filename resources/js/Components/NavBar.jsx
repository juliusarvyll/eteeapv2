import { Link } from '@inertiajs/react';
import React from 'react';

export default function NavBar() {
    return (
        <div className="bg-green-700 text-white py-4 shadow-lg">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <Link href="/" className="flex items-center space-x-4 hover:opacity-80 transition-opacity">
                    <img
                        src="/images/spup-logo.png"
                        alt="SPUP Logo"
                        className="h-12 w-auto"
                    />
                    <h1 className="text-lg font-bold font-old">St. Paul University Philippines</h1>
                </Link>
                <nav className="flex space-x-6">
                    <Link
                        href="/track-application"
                        className="hover:text-green-200 transition-colors"
                    >
                        Track Application
                    </Link>
                </nav>
            </div>
        </div>
    );
}
