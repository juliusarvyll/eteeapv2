import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import TextArea from '@/Components/TextArea';
import PrimaryButton from '@/Components/PrimaryButton';

export default function WorkExperienceStep({
    formData,
    errors,
    handleArrayFieldChange,
    addArrayItem,
    removeArrayItem,
    setFormData
}) {
    // Add debug logs
    console.log('WorkExperienceStep rendered with:', {
        formData,
        errors,
    });

    // Add more detailed debug logs
    console.log('WorkExperienceStep errors:', errors);

    // Initialize workExperiences if undefined
    if (!formData.workExperiences) {
        formData.workExperiences = [];
    }

    // Initialize employment_type if not set
    if (!formData.employment_type) {
        formData.employment_type = 'no_employment';
    }

    const handleEmploymentTypeChange = (value) => {
        const newFormData = {
            ...formData,
            employment_type: value,
            workExperiences: value !== 'no_employment' ? [{
                designation: '',
                companyName: '',
                companyAddress: '',
                dateFrom: '',
                dateTo: '',
                ...(value === 'employed' ? {
                    employmentStatus: '',
                    supervisorName: '',
                } : {}),
                ...(value === 'self_employed' ? {
                    reference1_name: '',
                    reference1_contact: '',
                    reference2_name: '',
                    reference2_contact: '',
                    reference3_name: '',
                    reference3_contact: '',
                } : {}),
                reasonForLeaving: '',
                responsibilities: '',
                documents: null
            }] : []
        };

        // Force state update
        setFormData(prev => ({
            ...newFormData,
            workExperiences: [...newFormData.workExperiences]
        }));
    };

    const handleFileChange = (index, e) => {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('File size should not exceed 2MB');
                e.target.value = '';
                return;
            }
            handleArrayFieldChange('workExperiences', index, 'documents', file);
        }
    };

    return (
        <div className="space-y-8">
            <div>
                <h2 className="text-xl font-bold mb-4">III. PAID WORK AND OTHER EXPERIENCES</h2>
                <p className="text-sm text-gray-600 mb-6">
                    Please select your employment status. If you have work experiences, you can add them below.
                </p>

                <div className="mb-6">
                    <h3 className="text-lg font-semibold mb-3">Employment Status</h3>
                    <div className="space-y-2">
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="employment_type"
                                value="employed"
                                checked={formData.employment_type === 'employed'}
                                onChange={(e) => handleEmploymentTypeChange(e.target.value)}
                                className="mr-2"
                            />
                            <span>Employed</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="employment_type"
                                value="self_employed"
                                checked={formData.employment_type === 'self_employed'}
                                onChange={(e) => handleEmploymentTypeChange(e.target.value)}
                                className="mr-2"
                            />
                            <span>Self-Employed</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="employment_type"
                                value="no_employment"
                                checked={formData.employment_type === 'no_employment'}
                                onChange={(e) => handleEmploymentTypeChange(e.target.value)}
                                className="mr-2"
                            />
                            <span>No Employment</span>
                        </label>
                    </div>
                </div>
            </div>

            {/* Show fields only for employed or self-employed */}
            {formData.employment_type !== 'no_employment' && (
                <>
                    {formData.workExperiences?.map((experience, index) => (
                        <div key={index} className="border p-4 rounded-lg space-y-4">
                            <div className="flex justify-between items-center">
                                <h3 className="text-lg font-semibold">Experience {index + 1}</h3>
                                {index > 0 && (
                                    <button
                                        type="button"
                                        onClick={() => removeArrayItem('workExperiences', index)}
                                        className="text-red-600 hover:text-red-800"
                                    >
                                        Remove
                                    </button>
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel value={formData.employment_type === 'employed' ? "Designation/Position" : "Business Name"} required />
                                    <TextInput
                                        name={`workExperiences.${index}.designation`}
                                        value={experience.designation}
                                        onChange={(e) => handleArrayFieldChange('workExperiences', index, 'designation', e.target.value)}
                                        className={`mt-1 block w-full ${errors[`workExperiences.${index}.designation`] ? 'border-red-500' : ''}`}
                                    />
                                    <InputError message={errors[`workExperiences.${index}.designation`]} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel value={formData.employment_type === 'employed' ? "Company Name" : "Business Address"} required />
                                    <TextInput
                                        name={`workExperiences.${index}.companyName`}
                                        value={experience.companyName}
                                        onChange={(e) => handleArrayFieldChange('workExperiences', index, 'companyName', e.target.value)}
                                        className={`mt-1 block w-full ${errors[`workExperiences.${index}.companyName`] ? 'border-red-500' : ''}`}
                                    />
                                    <InputError message={errors[`workExperiences.${index}.companyName`]} className="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Company Address" required />
                                <TextInput
                                    name={`workExperiences.${index}.companyAddress`}
                                    value={experience.companyAddress}
                                    onChange={(e) => handleArrayFieldChange('workExperiences', index, 'companyAddress', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`workExperiences.${index}.companyAddress`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`workExperiences.${index}.companyAddress`] && (
                                    <InputError message={errors[`workExperiences.${index}.companyAddress`]} className="mt-2" />
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel value="Year Started" required />
                                    <TextInput
                                        type="number"
                                        name={`workExperiences.${index}.dateFrom`}
                                        value={experience.dateFrom || ''}
                                        onChange={(e) => handleArrayFieldChange('workExperiences', index, 'dateFrom', e.target.value)}
                                        className={`mt-1 block w-full ${errors[`workExperiences.${index}.dateFrom`] ? 'border-red-500' : ''}`}
                                    />
                                    {errors[`workExperiences.${index}.dateFrom`] && (
                                        <InputError message={errors[`workExperiences.${index}.dateFrom`]} className="mt-2" />
                                    )}
                                </div>

                                <div>
                                    <InputLabel value="Year Ended" required />
                                    <TextInput
                                        type="number"
                                        name={`workExperiences.${index}.dateTo`}
                                        value={experience.dateTo || ''}
                                        onChange={(e) => handleArrayFieldChange('workExperiences', index, 'dateTo', e.target.value)}
                                        className={`mt-1 block w-full ${errors[`workExperiences.${index}.dateTo`] ? 'border-red-500' : ''}`}
                                    />
                                    {errors[`workExperiences.${index}.dateTo`] && (
                                        <InputError message={errors[`workExperiences.${index}.dateTo`]} className="mt-2" />
                                    )}
                                </div>
                            </div>

                            {/* Conditional fields based on employment type */}
                            {formData.employment_type === 'employed' ? (
                                <>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel value="Employment Status" required />
                                            <TextInput
                                                name={`workExperiences.${index}.employmentStatus`}
                                                value={experience.employmentStatus}
                                                onChange={(e) => handleArrayFieldChange('workExperiences', index, 'employmentStatus', e.target.value)}
                                                className={`mt-1 block w-full ${errors[`workExperiences.${index}.employmentStatus`] ? 'border-red-500' : ''}`}
                                            />
                                            <InputError message={errors[`workExperiences.${index}.employmentStatus`]} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel value="Supervisor Name" required />
                                            <TextInput
                                                name={`workExperiences.${index}.supervisorName`}
                                                value={experience.supervisorName}
                                                onChange={(e) => handleArrayFieldChange('workExperiences', index, 'supervisorName', e.target.value)}
                                                className={`mt-1 block w-full ${errors[`workExperiences.${index}.supervisorName`] ? 'border-red-500' : ''}`}
                                            />
                                            <InputError message={errors[`workExperiences.${index}.supervisorName`]} className="mt-2" />
                                        </div>
                                    </div>
                                </>
                            ) : (
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    {[1, 2, 3].map((refNum) => (
                                        <div key={refNum}>
                                            <InputLabel value={`Reference ${refNum} Name`} required />
                                            <TextInput
                                                name={`workExperiences.${index}.reference${refNum}_name`}
                                                value={experience[`reference${refNum}_name`]}
                                                onChange={(e) => handleArrayFieldChange('workExperiences', index, `reference${refNum}_name`, e.target.value)}
                                                className={`mt-1 block w-full ${errors[`workExperiences.${index}.reference${refNum}_name`] ? 'border-red-500' : ''}`}
                                            />
                                            <InputError message={errors[`workExperiences.${index}.reference${refNum}_name`]} className="mt-2" />

                                            <InputLabel value={`Reference ${refNum} Contact`} required className="mt-2" />
                                            <TextInput
                                                name={`workExperiences.${index}.reference${refNum}_contact`}
                                                value={experience[`reference${refNum}_contact`]}
                                                onChange={(e) => handleArrayFieldChange('workExperiences', index, `reference${refNum}_contact`, e.target.value)}
                                                className={`mt-1 block w-full ${errors[`workExperiences.${index}.reference${refNum}_contact`] ? 'border-red-500' : ''}`}
                                            />
                                            <InputError message={errors[`workExperiences.${index}.reference${refNum}_contact`]} className="mt-2" />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <div>
                                <InputLabel value="Reason for Leaving" required />
                                <TextArea
                                    name={`workExperiences.${index}.reasonForLeaving`}
                                    value={experience.reasonForLeaving}
                                    onChange={(e) => handleArrayFieldChange('workExperiences', index, 'reasonForLeaving', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`workExperiences.${index}.reasonForLeaving`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`workExperiences.${index}.reasonForLeaving`] && (
                                    <InputError message={errors[`workExperiences.${index}.reasonForLeaving`]} className="mt-2" />
                                )}
                            </div>

                            <div>
                                <InputLabel value="Responsibilities" required />
                                <TextArea
                                    name={`workExperiences.${index}.responsibilities`}
                                    value={experience.responsibilities}
                                    onChange={(e) => handleArrayFieldChange('workExperiences', index, 'responsibilities', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`workExperiences.${index}.responsibilities`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`workExperiences.${index}.responsibilities`] && (
                                    <InputError message={errors[`workExperiences.${index}.responsibilities`]} className="mt-2" />
                                )}
                            </div>

                            <div className="space-y-2">
                                <InputLabel value="Supporting Documents" />
                                <input
                                    type="file"
                                    onChange={(e) => handleFileChange(index, e)}
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    className={`block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100
                                        ${errors[`workExperiences.${index}.documents`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`workExperiences.${index}.documents`] && (
                                    <InputError message={errors[`workExperiences.${index}.documents`]} className="mt-2" />
                                )}
                            </div>
                        </div>
                    ))}

                    <PrimaryButton
                        type="button"
                        onClick={() => addArrayItem('workExperiences', {
                            designation: '',
                            companyName: '',
                            companyAddress: '',
                            dateFrom: '',
                            dateTo: '',
                            employmentStatus: '',
                            supervisorName: '',
                            reference1_name: '',
                            reference1_contact: '',
                            reference2_name: '',
                            reference2_contact: '',
                            reference3_name: '',
                            reference3_contact: '',
                            reasonForLeaving: '',
                            responsibilities: '',
                            documents: null
                        })}
                    >
                        {formData.employment_type === 'employed' ? 'Add Work Experience' : 'Add Business'}
                    </PrimaryButton>
                </>
            )}

            {/* Show message only for no employment */}
            {formData.employment_type === 'no_employment' && (
                <div className="p-4 bg-gray-50 rounded-lg">
                    <p className="text-gray-600">No employment history to display.</p>
                </div>
            )}
        </div>
    );
}
