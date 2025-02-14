import { useState } from 'react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextArea from '@/Components/TextArea';
import Modal from '@/Components/Modal';
import { useForm, router } from '@inertiajs/react';

export default function EssayStep({ 
    formData, 
    errors, 
    handleInputChange,
    onSubmit
}) {
    const [showConfirmation, setShowConfirmation] = useState(false);

    const renderPreviewSection = (title, fields) => (
        <div className="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 className="font-semibold text-lg mb-3">{title}</h3>
            <div className="space-y-2">
                {Object.entries(fields).map(([key, value]) => (
                    value && <div key={key} className="flex justify-between border-b pb-1">
                        <span className="text-gray-600 capitalize">{key.replace(/_/g, ' ')}:</span>
                        <span className="text-gray-800">{value}</span>
                    </div>
                ))}
            </div>
        </div>
    );

    const handleConfirmSubmit = async () => {
        try {
            // Pass the essay content to onSubmit
            await onSubmit(formData.essay);
            setShowConfirmation(false);
        } catch (error) {
            console.error('Submission failed:', error);
        }
    };

    return (
        <div className="space-y-8">
            <div>
                <h2 className="text-xl font-bold mb-4">VII. PERSONAL ESSAY</h2>
                <p className="text-sm text-gray-600 mb-2">
                    Please write an essay on the following topic, in the language you are most comfortable with.
                </p>
                <p className="text-base font-medium text-gray-800 mb-6 italic">
                    "If you finish your degree, how will this contribute to your personal development, 
                    to the development of your community, your work place, society and country?"
                </p>
            </div>

            <div className="space-y-4">
                <div>
                    <InputLabel 
                        htmlFor="essay" 
                        value="Your Essay" 
                        className="font-semibold"
                    />
                    <div className="mt-2">
                        <TextArea
                            id="essay"
                            name="essay"
                            value={formData.essay}
                            className="mt-1 block w-full"
                            rows={15}
                            placeholder="Write your essay here..."
                            onChange={handleInputChange}
                        />
                        <InputError message={errors.essay} className="mt-2" />
                    </div>
                </div>

                <div className="text-sm text-gray-500">
                    <p>Tips for writing your essay:</p>
                    <ul className="list-disc list-inside ml-4 space-y-1 mt-2">
                        <li>Be clear and concise in your writing</li>
                        <li>Structure your essay with an introduction, body paragraphs, and conclusion</li>
                        <li>Address all aspects of the topic: personal, community, workplace, society, and country</li>
                        <li>Use specific examples to support your points</li>
                        <li>Proofread your essay before submitting</li>
                    </ul>
                </div>

                <div className="text-sm text-gray-500">
                    <p className="font-medium">Consider addressing these points:</p>
                    <ul className="list-disc list-inside ml-4 space-y-1 mt-2">
                        <li>Personal growth and career advancement</li>
                        <li>Impact on your family and immediate community</li>
                        <li>Contributions to your workplace and industry</li>
                        <li>Broader societal impact</li>
                        <li>National development and progress</li>
                    </ul>
                </div>
            </div>

            <div className="mt-8 border-t pt-6">
                <button
                    type="button"
                    onClick={() => setShowConfirmation(true)}
                    className="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Submit Application
                </button>
            </div>

            <Modal
                show={showConfirmation}
                title="Confirm Submission"
                maxWidth="3xl"
                onClose={() => setShowConfirmation(false)}
            >
                <div className="space-y-4">
                    <p className="text-red-600 font-semibold">
                        Please review your application before submitting:
                    </p>

                    {renderPreviewSection('Personal Information', {
                        'First Name': formData.firstName,
                        'Last Name': formData.lastName,
                        'Middle Name': formData.middleName,
                        'Suffix': formData.suffix,
                        'Birth Date': formData.birthDate,
                        'Place of Birth': formData.placeOfBirth,
                        'Civil Status': formData.civilStatus,
                        'Sex': formData.sex,
                        'Address': formData.address,
                        'Contact Number': formData.contactNumber,
                        'Nationality': formData.nationality
                    })}

                    {renderPreviewSection('Education Background', {
                        'Elementary School': formData.elementarySchool,
                        'Secondary Education Type': formData.secondaryEducationType,
                        'Post Secondary Education': formData.postSecondary
                            .map(edu => `${edu.program} at ${edu.institution}`)
                            .join(', '),
                        'Non-Formal Education': formData.nonFormalEducation
                            .map(edu => edu.title)
                            .join(', ')
                    })}

                    {renderPreviewSection('Work Experience', {
                        'Total Experiences': formData.workExperiences.length,
                        'Recent Position': formData.workExperiences[0]?.designation,
                        'Recent Company': formData.workExperiences[0]?.companyName,
                        'Employment Years': formData.workExperiences
                            .reduce((total, exp) => total + (exp.dateTo - exp.dateFrom), 0)
                    })}

                    {renderPreviewSection('Awards & Recognition', {
                        'Academic Awards': formData.academicAwards.length,
                        'Community Awards': formData.communityAwards.length,
                        'Work Awards': formData.workAwards.length
                    })}

                    {renderPreviewSection('Documents', {
                        'Birth Certificate': formData.birthCertificate ? 'Uploaded' : 'Missing',
                        'Marriage Certificate': formData.marriageCertificate ? 'Uploaded' : 'N/A',
                        'Legal Documents': formData.legalDocument ? 'Uploaded' : 'Missing'
                    })}

                    <div className="mt-8 flex justify-end gap-4">
                        <button
                            type="button"
                            onClick={() => setShowConfirmation(false)}
                            className="px-4 py-2 text-gray-600 hover:text-gray-800"
                        >
                            Go Back and Edit
                        </button>
                        <button
                            type="button"
                            onClick={handleConfirmSubmit}
                            className="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700"
                        >
                            Confirm and Submit
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    );
} 