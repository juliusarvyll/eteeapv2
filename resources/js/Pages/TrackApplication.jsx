import { Head, useForm, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import InputError from '@/Components/InputError';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import axios from 'axios';
import NavBar from '@/Components/NavBar';

export default function TrackApplication() {
    const [application, setApplication] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [activeTab, setActiveTab] = useState('overview');
    const [previewDocument, setPreviewDocument] = useState(null);
    const [viewingDocument, setViewingDocument] = useState(null);

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

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    // Document preview helpers
    const isImage = (url) => {
        return /\.(jpg|jpeg|png|gif|webp)$/i.test(url);
    };

    const isPdf = (url) => {
        return /\.pdf$/i.test(url);
    };

    const renderDocumentThumbnail = (url, label = "Document") => {
        if (!url) return null;

        return (
            <div className="mt-3 flex items-center space-x-2">
                <div
                    className="w-12 h-12 border rounded-md flex items-center justify-center cursor-pointer overflow-hidden bg-gray-100"
                    onClick={() => setPreviewDocument({ url, label })}
                >
                    {isImage(url) ? (
                        <img src={url} alt={label} className="object-cover w-full h-full" />
                    ) : (
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    )}
                </div>
                <div className="flex flex-col">
                    <span
                        className="text-blue-600 text-sm cursor-pointer hover:underline"
                        onClick={() => setPreviewDocument({ url, label })}
                    >
                        {label}
                    </span>
                    <button
                        onClick={() => setViewingDocument({ url, label })}
                        className="text-xs text-gray-600 hover:text-gray-800 mt-1"
                    >
                        View in iframe
                    </button>
                </div>
            </div>
        );
    };

    const DocumentPreviewModal = ({ document, onClose }) => {
        if (!document) return null;

        return (
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75">
                <div className="bg-white rounded-lg max-w-4xl w-full h-5/6 flex flex-col overflow-hidden">
                    <div className="p-4 border-b flex justify-between items-center">
                        <h3 className="text-lg font-medium">{document.label}</h3>
                        <button
                            onClick={onClose}
                            className="text-gray-500 hover:text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div className="flex-1 overflow-auto p-4 flex items-center justify-center bg-gray-100">
                        {isImage(document.url) ? (
                            <img
                                src={document.url}
                                alt={document.label}
                                className="max-h-full object-contain"
                            />
                        ) : isPdf(document.url) ? (
                            <iframe
                                src={`${document.url}#toolbar=0&navpanes=0`}
                                className="w-full h-full"
                                title={document.label}
                            />
                        ) : (
                            <div className="text-center p-6">
                                <p>This file type cannot be previewed.</p>
                                <a
                                    href={document.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                >
                                    Download File
                                </a>
                            </div>
                        )}
                    </div>
                    <div className="p-4 border-t flex justify-end">
                        <a
                            href={document.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="px-4 py-2 text-sm text-blue-600 hover:text-blue-800"
                        >
                            Open in New Tab
                        </a>
                        <button
                            onClick={onClose}
                            className="ml-2 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        );
    };

    const IframeDocumentViewer = ({ document, onClose }) => {
        if (!document) return null;

        return (
            <div className="fixed inset-0 z-50 flex flex-col bg-white">
                <div className="p-4 bg-gray-100 border-b flex justify-between items-center shadow-sm">
                    <h3 className="text-lg font-medium truncate">{document.label}</h3>
                    <div className="flex items-center space-x-2">
                        <a
                            href={document.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            Download
                        </a>
                        <button
                            onClick={onClose}
                            className="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 flex items-center"
                        >
                            <span className="mr-1">Close</span>
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div className="flex-1 bg-gray-50">
                    {isImage(document.url) ? (
                        <iframe
                            src={document.url}
                            className="w-full h-full border-0"
                            title={document.label}
                            allowFullScreen
                        />
                    ) : isPdf(document.url) ? (
                        <iframe
                            src={`${document.url}#toolbar=1&navpanes=1&scrollbar=1`}
                            className="w-full h-full border-0"
                            title={document.label}
                            allowFullScreen
                        />
                    ) : (
                        <div className="h-full flex items-center justify-center">
                            <div className="text-center p-6">
                                <p className="text-xl font-medium text-gray-700 mb-3">This file type cannot be displayed in an iframe</p>
                                <a
                                    href={document.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                >
                                    Open File in New Tab
                                </a>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        );
    };

    const renderTabContent = () => {
        if (!application) return null;

        switch (activeTab) {
            case 'overview':
                return (
                    <div className="space-y-4">
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <h3 className="font-medium text-gray-900">Application Summary</h3>
                            <div className="mt-2 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p className="text-gray-500">Application ID</p>
                                    <p className="font-medium">{application.id}</p>
                                </div>
                                <div>
                                    <p className="text-gray-500">Submitted On</p>
                                    <p className="font-medium">{application.submitted_at}</p>
                                </div>
                                <div>
                                    <p className="text-gray-500">Status</p>
                                    <p className={`px-2 py-1 rounded-full text-xs font-medium inline-block ${statusColors[application.status]}`}>
                                        {application.status.toUpperCase()}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-gray-500">Last Updated</p>
                                    <p className="font-medium">{formatDate(application.personal_info?.updated_at) || 'N/A'}</p>
                                </div>
                            </div>
                        </div>

                        {application.personal_info && (
                            <div className="bg-white p-4 rounded-lg border">
                                <h3 className="font-medium text-gray-900">Personal Information</h3>
                                <div className="mt-2 grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p className="text-gray-500">Full Name</p>
                                        <p className="font-medium">
                                            {application.personal_info.firstName} {application.personal_info.middleName ? application.personal_info.middleName + ' ' : ''}{application.personal_info.lastName}
                                            {application.personal_info.suffix ? ', ' + application.personal_info.suffix : ''}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Email</p>
                                        <p className="font-medium">{application.personal_info.email}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Phone</p>
                                        <p className="font-medium">{application.personal_info.phoneNumber}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Date of Birth</p>
                                        <p className="font-medium">{formatDate(application.personal_info.birthDate)}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Gender</p>
                                        <p className="font-medium">{application.personal_info.sex}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Civil Status</p>
                                        <p className="font-medium">{application.personal_info.civilStatus}</p>
                                    </div>
                                </div>

                                {/* Add document preview for personal documents */}
                                {application.personal_info.document &&
                                    renderDocumentThumbnail(
                                        application.personal_info.document,
                                        "Personal Document"
                                    )
                                }
                            </div>
                        )}

                        {application.learning_objective && (
                            <div className="bg-white p-4 rounded-lg border">
                                <h3 className="font-medium text-gray-900">Learning Objectives</h3>
                                <div className="mt-2 space-y-2 text-sm">
                                    <div>
                                        <p className="text-gray-500">First Priority</p>
                                        <p className="font-medium">{application.learning_objective.firstPriority}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Second Priority</p>
                                        <p className="font-medium">{application.learning_objective.secondPriority}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Third Priority</p>
                                        <p className="font-medium">{application.learning_objective.thirdPriority}</p>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                );

            case 'education':
                return (
                    <div className="space-y-6">
                        {application.education?.length > 0 ? (
                            application.education.map((edu, index) => (
                                <div key={index} className="bg-white p-4 rounded-lg border">
                                    <h3 className="font-medium text-gray-900">{edu.type.replace('_', ' ').charAt(0).toUpperCase() + edu.type.replace('_', ' ').slice(1)} Education</h3>
                                    <div className="mt-2 grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p className="text-gray-500">School</p>
                                            <p className="font-medium">{edu.school_name}</p>
                                        </div>
                                        <div>
                                            <p className="text-gray-500">Address</p>
                                            <p className="font-medium">{edu.address}</p>
                                        </div>
                                        <div>
                                            <p className="text-gray-500">Period</p>
                                            <p className="font-medium">{edu.date_from} - {edu.date_to}</p>
                                        </div>
                                        {edu.type === 'high_school' && (
                                            <div>
                                                <p className="text-gray-500">Strand</p>
                                                <p className="font-medium">{edu.strand || 'N/A'}</p>
                                            </div>
                                        )}
                                    </div>

                                    {/* Add diploma file preview */}
                                    {edu.diploma_files && edu.diploma_files.length > 0 && (
                                        <div className="mt-4">
                                            <p className="text-gray-500 text-sm">Diploma/Certificate</p>
                                            <div className="flex flex-wrap gap-2 mt-2">
                                                {edu.diploma_files.map((file, idx) =>
                                                    renderDocumentThumbnail(
                                                        file,
                                                        `${edu.type.charAt(0).toUpperCase() + edu.type.slice(1)} Diploma ${idx + 1}`
                                                    )
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-6 text-gray-500">No education records found.</div>
                        )}
                    </div>
                );

            case 'workExperience':
                return (
                    <div className="space-y-6">
                        {application.work_experience?.length > 0 ? (
                            application.work_experience.map((work, index) => (
                                <div key={index} className="bg-white p-4 rounded-lg border">
                                    <div className="flex justify-between">
                                        <h3 className="font-medium text-gray-900">{work.designation}</h3>
                                        <span className="text-sm text-gray-500">{work.dateFrom} - {work.dateTo}</span>
                                    </div>
                                    <p className="text-gray-700 mt-1">{work.companyName}</p>
                                    <p className="text-gray-500 text-sm mt-1">{work.companyAddress}</p>

                                    <div className="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        {work.employment_type === 'employed' && (
                                            <>
                                                <div>
                                                    <p className="text-gray-500">Employment Status</p>
                                                    <p className="font-medium">{work.employmentStatus}</p>
                                                </div>
                                                <div>
                                                    <p className="text-gray-500">Supervisor</p>
                                                    <p className="font-medium">{work.supervisorName}</p>
                                                </div>
                                            </>
                                        )}

                                        {work.employment_type === 'self_employed' && (
                                            <div className="md:col-span-2">
                                                <p className="text-gray-500">References</p>
                                                <ul className="mt-1 space-y-1">
                                                    {work.reference1_name && (
                                                        <li>{work.reference1_name} - {work.reference1_contact}</li>
                                                    )}
                                                    {work.reference2_name && (
                                                        <li>{work.reference2_name} - {work.reference2_contact}</li>
                                                    )}
                                                    {work.reference3_name && (
                                                        <li>{work.reference3_name} - {work.reference3_contact}</li>
                                                    )}
                                                </ul>
                                            </div>
                                        )}

                                        <div className="md:col-span-2">
                                            <p className="text-gray-500">Reason for Leaving</p>
                                            <p className="font-medium">{work.reasonForLeaving}</p>
                                        </div>

                                        <div className="md:col-span-2">
                                            <p className="text-gray-500">Responsibilities</p>
                                            <p className="font-medium">{work.responsibilities}</p>
                                        </div>

                                        {/* Replace link with thumbnail preview */}
                                        {work.documents && (
                                            <div className="md:col-span-2">
                                                <p className="text-gray-500">Supporting Documents</p>
                                                {renderDocumentThumbnail(
                                                    work.documents_url,
                                                    `Work Experience - ${work.designation} Documents`
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-6 text-gray-500">No work experience records found.</div>
                        )}
                    </div>
                );

            case 'awards':
                return (
                    <div className="space-y-6">
                        {/* Academic Awards */}
                        <div>
                            <h3 className="font-medium text-gray-900 mb-4">Academic Awards</h3>
                            {application.academic_awards?.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4">
                                    {application.academic_awards.map((award, index) => (
                                        <div key={index} className="bg-white p-4 rounded-lg border">
                                            <div className="flex justify-between">
                                                <h4 className="font-medium">{award.title}</h4>
                                                <span className="text-sm text-gray-500">{formatDate(award.dateReceived)}</span>
                                            </div>
                                            <p className="text-gray-700 mt-1">{award.institution}</p>
                                            <p className="text-gray-600 text-sm mt-2">{award.description}</p>

                                            {/* Replace link with thumbnail preview */}
                                            {award.document &&
                                                renderDocumentThumbnail(
                                                    award.document_url,
                                                    `Academic Award - ${award.title}`
                                                )
                                            }
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-4 text-gray-500 bg-white p-4 rounded-lg border">
                                    No academic awards found.
                                </div>
                            )}
                        </div>

                        {/* Community Awards */}
                        <div>
                            <h3 className="font-medium text-gray-900 mb-4">Community Awards</h3>
                            {application.community_awards?.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4">
                                    {application.community_awards.map((award, index) => (
                                        <div key={index} className="bg-white p-4 rounded-lg border">
                                            <div className="flex justify-between">
                                                <h4 className="font-medium">{award.title}</h4>
                                                <span className="text-sm text-gray-500">{formatDate(award.dateAwarded)}</span>
                                            </div>
                                            <p className="text-gray-700 mt-1">{award.organization}</p>

                                            {/* Add document preview if available */}
                                            {award.document &&
                                                renderDocumentThumbnail(
                                                    award.document_url,
                                                    `Community Award - ${award.title}`
                                                )
                                            }
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-4 text-gray-500 bg-white p-4 rounded-lg border">
                                    No community awards found.
                                </div>
                            )}
                        </div>

                        {/* Work Awards */}
                        <div>
                            <h3 className="font-medium text-gray-900 mb-4">Work Awards</h3>
                            {application.work_awards?.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4">
                                    {application.work_awards.map((award, index) => (
                                        <div key={index} className="bg-white p-4 rounded-lg border">
                                            <div className="flex justify-between">
                                                <h4 className="font-medium">{award.title}</h4>
                                                <span className="text-sm text-gray-500">{formatDate(award.dateAwarded)}</span>
                                            </div>
                                            <p className="text-gray-700 mt-1">{award.organization}</p>

                                            {/* Replace link with thumbnail preview */}
                                            {award.document &&
                                                renderDocumentThumbnail(
                                                    award.document_url,
                                                    `Work Award - ${award.title}`
                                                )
                                            }
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-4 text-gray-500 bg-white p-4 rounded-lg border">
                                    No work awards found.
                                </div>
                            )}
                        </div>
                    </div>
                );

            case 'creativeWorks':
                return (
                    <div className="space-y-4">
                        {application.creative_works?.length > 0 ? (
                            application.creative_works.map((work, index) => (
                                <div key={index} className="bg-white p-4 rounded-lg border">
                                    <div className="flex justify-between">
                                        <h3 className="font-medium text-gray-900">{work.title}</h3>
                                        <span className="text-sm text-gray-500">{formatDate(work.date_completed)}</span>
                                    </div>

                                    <div className="mt-3 space-y-3 text-sm">
                                        <div>
                                            <p className="text-gray-500">Description</p>
                                            <p className="font-medium">{work.description}</p>
                                        </div>

                                        <div>
                                            <p className="text-gray-500">Significance</p>
                                            <p className="font-medium">{work.significance}</p>
                                        </div>

                                        {work.corroborating_body && (
                                            <div>
                                                <p className="text-gray-500">Corroborating Body</p>
                                                <p className="font-medium">{work.corroborating_body}</p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-6 text-gray-500 bg-white p-4 rounded-lg border">
                                No creative works found.
                            </div>
                        )}
                    </div>
                );

            case 'lifelong':
                const categories = {
                    'hobby': 'Hobbies & Leisure Activities',
                    'skill': 'Special Skills',
                    'work': 'Work Activities',
                    'volunteer': 'Volunteer Activities',
                    'travel': 'Travels'
                };

                // Group by type
                const groupedLearning = application.lifelong_learning?.reduce((acc, item) => {
                    if (!acc[item.type]) {
                        acc[item.type] = [];
                    }
                    acc[item.type].push(item);
                    return acc;
                }, {});

                return (
                    <div className="space-y-6">
                        {Object.keys(categories).map(type => (
                            <div key={type}>
                                <h3 className="font-medium text-gray-900 mb-3">{categories[type]}</h3>
                                {groupedLearning && groupedLearning[type]?.length > 0 ? (
                                    <div className="bg-white p-4 rounded-lg border">
                                        <ul className="list-disc pl-5 space-y-2">
                                            {groupedLearning[type].map((item, idx) => (
                                                <li key={idx} className="text-gray-700">{item.description}</li>
                                            ))}
                                        </ul>
                                    </div>
                                ) : (
                                    <div className="text-center py-4 text-gray-500 bg-white p-4 rounded-lg border">
                                        No {categories[type].toLowerCase()} found.
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                );

            case 'essay':
                return (
                    <div className="space-y-4">
                        {application.essay ? (
                            <div className="bg-white p-6 rounded-lg border">
                                <h3 className="font-medium text-gray-900 mb-4">Application Essay</h3>
                                <div className="prose max-w-none">
                                    {application.essay.content.split('\n').map((paragraph, idx) => (
                                        paragraph ? <p key={idx} className="mb-4">{paragraph}</p> : <br key={idx} />
                                    ))}
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-6 text-gray-500 bg-white p-4 rounded-lg border">
                                No essay found.
                            </div>
                        )}
                    </div>
                );

            default:
                return null;
        }
    };

    // Handle Escape key to close the preview modal
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (e.key === 'Escape') {
                setPreviewDocument(null);
                setViewingDocument(null);
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, []);

    return (
        <>
            <Head title="Track Application Status" />
            <NavBar />
            <div className="min-h-screen bg-gray-50 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mx-auto">
                        <h1 className="text-3xl font-bold text-gray-900 mb-8">Track Your Application</h1>

                        <form onSubmit={handleSubmit} className="bg-white p-6 rounded-lg shadow-sm mb-8">
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
                            </div>
                        </form>

                        {application && (
                            <div className="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div className="p-6 border-b">
                                    <div className="flex justify-between items-center">
                                        <h2 className="text-xl font-semibold text-gray-900">
                                            Application Details
                                        </h2>
                                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${statusColors[application.status]}`}>
                                            {application.status.toUpperCase()}
                                        </span>
                                    </div>
                                    <p className="text-gray-600 mt-1">
                                        Submitted on {application.submitted_at}
                                    </p>
                                </div>

                                <div className="border-b border-gray-200">
                                    <nav className="flex -mb-px overflow-x-auto">
                                        <button
                                            onClick={() => setActiveTab('overview')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'overview'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Overview
                                        </button>

                                        <button
                                            onClick={() => setActiveTab('education')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'education'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Education
                                        </button>

                                        <button
                                            onClick={() => setActiveTab('workExperience')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'workExperience'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Work Experience
                                        </button>

                                        <button
                                            onClick={() => setActiveTab('awards')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'awards'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Awards
                                        </button>

                                        <button
                                            onClick={() => setActiveTab('creativeWorks')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'creativeWorks'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Creative Works
                                        </button>

                                        <button
                                            onClick={() => setActiveTab('lifelong')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'lifelong'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Lifelong Learning
                                        </button>

                                        <button
                                            onClick={() => setActiveTab('essay')}
                                            className={`whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm ${
                                                activeTab === 'essay'
                                                    ? 'border-indigo-500 text-indigo-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            }`}
                                        >
                                            Essay
                                        </button>
                                    </nav>
                                </div>

                                <div className="p-6">
                                    {renderTabContent()}
                                </div>
                            </div>
                        )}

                        {/* Document Preview Modal */}
                        {previewDocument && (
                            <DocumentPreviewModal
                                document={previewDocument}
                                onClose={() => setPreviewDocument(null)}
                            />
                        )}

                        {/* Full-page iframe document viewer */}
                        {viewingDocument && (
                            <IframeDocumentViewer
                                document={viewingDocument}
                                onClose={() => setViewingDocument(null)}
                            />
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
