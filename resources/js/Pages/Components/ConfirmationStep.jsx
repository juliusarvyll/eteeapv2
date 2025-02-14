import { useEffect } from 'react';
import { Link } from '@inertiajs/react';

export default function ConfirmationStep({ formData }) {
    useEffect(() => {
        // Add validation logging
        if (!formData?.applicant_id) {
            console.error('ConfirmationStep: Missing applicant_id in formData', formData);
        } else {
            console.log('ConfirmationStep: Received applicant_id:', formData.applicant_id);
        }
    }, [formData]);

    // Add null check to prevent rendering without applicant_id
    if (!formData?.applicant_id) {
        return (
            <div className="text-center text-red-600">
                Error: Application ID not found. Please try again.
            </div>
        );
    }

    return (
        <div className="space-y-8">
            <div className="text-center">
                <div className="mb-6">
                    <svg 
                        className="mx-auto h-16 w-16 text-green-500" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path 
                            strokeLinecap="round" 
                            strokeLinejoin="round" 
                            strokeWidth="2" 
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
                
                <h2 className="text-2xl font-bold mb-4">Application Submitted Successfully!</h2>
                
                <div className="bg-gray-50 rounded-lg p-6 mb-6 inline-block">
                    <p className="text-sm text-gray-600 mb-2">Your Application Number:</p>
                    <p className="text-3xl font-bold text-gray-800">{formData.applicant_id}</p>
                </div>

                <div className="space-y-4">
                    <p className="text-gray-600">
                        Please save your application number for future reference. You will need this 
                        number to track your application status.
                    </p>
                    
                    <div className="text-sm text-gray-500">
                        <p>What happens next?</p>
                        <ul className="list-disc list-inside mt-2 space-y-1">
                            <li>Our team will review your application</li>
                            <li>You will receive an email notification about your application status</li>
                            <li>Additional documents may be requested if needed</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div className="flex justify-center space-x-4">
                <Link
                    href={route('Dashboard')}
                    className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Return to Dashboard
                </Link>
                <button
                    onClick={() => window.print()}
                    className="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    Print Confirmation
                </button>
            </div>
        </div>
    );
} 