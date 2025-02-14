import { Link } from '@inertiajs/react';
import { Head } from '@inertiajs/react';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome to ETEEAP - SPUP" />

            <div className="relative min-h-screen bg-gray-100">
                {/* Header Section */}
                <div className="bg-green-700 text-white py-4">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex items-center">
                        <img
                            src="images/spup-logo.png"
                            alt="SPUP Logo"
                            className="h-12 w-auto"
                        />
                        <h1 className="text-lg font-bold font-old">St. Paul University Philippines</h1>
                    </div>
                </div>

                {/* Hero Section */}
                <div className="relative overflow-hidden bg-white">
                    <div className="mx-auto max-w-7xl">
                        <div className="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:w-full lg:max-w-2xl lg:pb-28 xl:pb-32">
                            <main className="mx-auto mt-10 max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                                <div className="sm:text-center lg:text-left">
                                    <h1 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                                        <span className="block">Welcome to</span>
                                        <span className="block text-green-700">ETEEAP</span>
                                    </h1>
                                    <p className="mt-3 text-base text-gray-500 sm:mx-auto sm:mt-5 sm:max-w-xl sm:text-lg md:mt-5 md:text-xl lg:mx-0">
                                        Expanded Tertiary Education Equivalency and Accreditation Program - 
                                        Your pathway to recognizing learning through work and life experiences.
                                    </p>
                                    <div className="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-3">
                                        <div className="rounded-md shadow">
                                            <Link
                                                href="/dashboard"
                                                className="flex w-full items-center justify-center rounded-md border border-transparent bg-green-700 px-8 py-3 text-base font-medium text-white hover:bg-green-800 md:px-10 md:py-4 md:text-lg"
                                            >
                                                Start Application
                                            </Link>
                                        </div>
                                        <div className="mt-3 sm:mt-0">
                                            <Link
                                                href="/track-application"
                                                className="flex w-full items-center justify-center rounded-md border border-transparent bg-blue-600 px-8 py-3 text-base font-medium text-white hover:bg-blue-700 md:px-10 md:py-4 md:text-lg"
                                            >
                                                Track Application
                                            </Link>
                                        </div>
                                        <div className="mt-3 sm:ml-3 sm:mt-0">
                                            <Link
                                                href="/about"
                                                className="flex w-full items-center justify-center rounded-md border border-transparent bg-green-100 px-8 py-3 text-base font-medium text-green-700 hover:bg-green-200 md:px-10 md:py-4 md:text-lg"
                                            >
                                                Learn More
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </main>
                        </div>
                    </div>
                    
                    {/* Features Section */}
                    <div className="bg-gray-50 py-12 sm:py-16">
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="lg:text-center">
                                <h2 className="text-lg font-semibold text-green-700">ETEEAP Benefits</h2>
                                <p className="mt-2 text-3xl font-bold leading-8 tracking-tight text-gray-900 sm:text-4xl">
                                    Why Choose ETEEAP at SPUP?
                                </p>
                            </div>

                            <div className="mt-10">
                                <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                                    {/* Feature 1 */}
                                    <div className="pt-6">
                                        <div className="flow-root rounded-lg bg-white px-6 pb-8">
                                            <div className="-mt-6">
                                                <h3 className="mt-8 text-lg font-medium tracking-tight text-gray-900">
                                                    Recognition of Experience
                                                </h3>
                                                <p className="mt-5 text-base text-gray-500">
                                                    Get academic credit for your professional experience and prior learning.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Feature 2 */}
                                    <div className="pt-6">
                                        <div className="flow-root rounded-lg bg-white px-6 pb-8">
                                            <div className="-mt-6">
                                                <h3 className="mt-8 text-lg font-medium tracking-tight text-gray-900">
                                                    Flexible Learning
                                                </h3>
                                                <p className="mt-5 text-base text-gray-500">
                                                    Balance your education with work and personal commitments.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Feature 3 */}
                                    <div className="pt-6">
                                        <div className="flow-root rounded-lg bg-white px-6 pb-8">
                                            <div className="-mt-6">
                                                <h3 className="mt-8 text-lg font-medium tracking-tight text-gray-900">
                                                    Career Advancement
                                                </h3>
                                                <p className="mt-5 text-base text-gray-500">
                                                    Enhance your qualifications and open new career opportunities.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
