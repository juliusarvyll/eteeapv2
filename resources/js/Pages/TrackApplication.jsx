import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/Components/InputError';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import axios from 'axios';
import NavBar from '@/Components/NavBar';

export default function TrackApplication() {
    const [application, setApplication] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    
    const { data, setData } = useForm({
        applicant_id: ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);
        
        axios.get('/track-application', {
            params: {
                applicant_id: data.applicant_id
            }
        })
        .then(response => {
            setApplication(response.data.application);
            setLoading(false);
        })
        .catch(error => {
            setError(error.response?.data?.message || 'Failed to fetch application');
            setLoading(false);
        });
    };

    const statusColors = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        review: 'bg-blue-100 text-blue-800'
    };

    return (
        <>
            <Head title="Track Application Status" />
            <NavBar />
            <div className="min-h-screen bg-gray-50 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mx-auto">
                        <h1 className="text-3xl font-bold text-gray-900 mb-8">Track Your Application</h1>
                        
                        <form onSubmit={handleSubmit} className="bg-white p-6 rounded-lg shadow-sm">
                            <div className="space-y-6">
                                <div>
                                    <label htmlFor="applicant_id" className="block text-sm font-medium text-gray-700">
                                        Application ID
                                    </label>
                                    <div className="mt-1 flex gap-4">
                                        <TextInput
                                            id="applicant_id"
                                            value={data.applicant_id}
                                            onChange={(e) => setData('applicant_id', e.target.value)}
                                            className="flex-1"
                                            placeholder="Enter your application ID (e.g. APP-2024-00001)"
                                        />
                                        <PrimaryButton disabled={loading}>
                                            {loading ? 'Searching...' : 'Track Status'}
                                        </PrimaryButton>
                                    </div>
                                    <InputError message={error} className="mt-2" />
                                </div>

                                {application && (
                                    <div className="border-t pt-6">
                                        <h2 className="text-lg font-semibold mb-4">Application Details</h2>
                                        
                                        <div className="space-y-4">
                                            <div className="flex items-center justify-between">
                                                <span className="text-gray-600">Application ID:</span>
                                                <span className="font-medium">{application.id}</span>
                                            </div>
                                            
                                            <div className="flex items-center justify-between">
                                                <span className="text-gray-600">Status:</span>
                                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${statusColors[application.status]}`}>
                                                    {application.status.toUpperCase()}
                                                </span>
                                            </div>
                                            
                                            <div className="flex items-center justify-between">
                                                <span className="text-gray-600">Submitted On:</span>
                                                <span className="font-medium">{application.submitted_at}</span>
                                            </div>
                                            
                                            {application.personal_info && (
                                                <div className="mt-6 space-y-2">
                                                    <h3 className="text-md font-semibold">Personal Information</h3>
                                                    <p>{application.personal_info.firstName} {application.personal_info.lastName}</p>
                                                    <p>{application.personal_info.email}</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </form>

                        {application?.work_experience && (
                            <div className="mt-8 bg-white p-6 rounded-lg shadow-sm">
                                <h3 className="text-lg font-semibold mb-4">Work Experience</h3>
                                <div className="space-y-4">
                                    {application.work_experience.map((work, index) => (
                                        <div key={index} className="border-b pb-4 last:border-0">
                                            <p className="font-medium">{work.designation}</p>
                                            <p className="text-gray-600">{work.companyName}</p>
                                            <p className="text-sm text-gray-500">
                                                {work.dateFrom} - {work.dateTo}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
} 