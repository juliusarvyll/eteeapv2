import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import TextArea from '@/Components/TextArea';
import PrimaryButton from '@/Components/PrimaryButton';

export default function HonorsAwardsStep({
    formData,
    errors,
    handleArrayFieldChange,
    addArrayItem,
    removeArrayItem
}) {
    // Add debug logs
    console.log('HonorsAwardsStep errors:', errors);

    return (
        <div className="space-y-8">
            <div>
                <h2 className="text-xl font-bold mb-4">IV. HONORS, AWARDS AND CITATIONS RECEIVED</h2>
                <p className="text-sm text-gray-600 mb-6">
                    In this section, please describe all the awards you have received from schools,
                    community and civic organizations, as well as citations for work excellence,
                    outstanding accomplishments, community service, etc.
                </p>
            </div>

            {/* Academic Awards */}
            <section className="space-y-4">
                <h2 className="text-xl font-bold">Academic Awards and Honors</h2>
                <p className="text-sm text-gray-600">List any academic awards, honors, or recognition you have received.</p>

                {formData.academicAwards?.map((award, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <div className="flex justify-between items-center">
                            <h3 className="text-lg font-semibold">Award {index + 1}</h3>
                            {index > 0 && (
                                <button
                                    type="button"
                                    onClick={() => removeArrayItem('academicAwards', index)}
                                    className="text-red-600 hover:text-red-800"
                                >
                                    Remove
                                </button>
                            )}
                        </div>

                        <div className="space-y-4">
                            <div>
                                <InputLabel value="Award/Honor Title" required />
                                <TextInput
                                    name={`academicAwards.${index}.title`}
                                    value={award.title}
                                    onChange={(e) => handleArrayFieldChange('academicAwards', index, 'title', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`academicAwards.${index}.title`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`academicAwards.${index}.title`] && (
                                    <InputError message={errors[`academicAwards.${index}.title`]} className="mt-2" />
                                )}
                            </div>

                            <div>
                                <InputLabel value="Awarding Institution" required />
                                <TextInput
                                    name={`academicAwards.${index}.institution`}
                                    value={award.institution}
                                    onChange={(e) => handleArrayFieldChange('academicAwards', index, 'institution', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`academicAwards.${index}.institution`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`academicAwards.${index}.institution`] && (
                                    <InputError message={errors[`academicAwards.${index}.institution`]} className="mt-2" />
                                )}
                            </div>

                            <div>
                                <InputLabel value="Date Received" required />
                                <TextInput
                                    type="date"
                                    name={`academicAwards.${index}.dateReceived`}
                                    value={award.dateReceived}
                                    onChange={(e) => handleArrayFieldChange('academicAwards', index, 'dateReceived', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`academicAwards.${index}.dateReceived`] ? 'border-red-500' : ''}`}
                                />
                                {errors[`academicAwards.${index}.dateReceived`] && (
                                    <InputError message={errors[`academicAwards.${index}.dateReceived`]} className="mt-2" />
                                )}
                            </div>

                            <div>
                                <InputLabel value="Description" required />
                                <TextArea
                                    name={`academicAwards.${index}.description`}
                                    value={award.description}
                                    onChange={(e) => handleArrayFieldChange('academicAwards', index, 'description', e.target.value)}
                                    className={`mt-1 block w-full ${errors[`academicAwards.${index}.description`] ? 'border-red-500' : ''}`}
                                    rows={3}
                                />
                                {errors[`academicAwards.${index}.description`] && (
                                    <InputError message={errors[`academicAwards.${index}.description`]} className="mt-2" />
                                )}
                            </div>

                            <div>
                                <InputLabel value="Supporting Document" />
                                <input
                                    type="file"
                                    name={`academicAwards.${index}.document`}
                                    onChange={(e) => handleArrayFieldChange('academicAwards', index, 'document', e.target.files[0])}
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    className={`mt-1 block w-full ${errors[`academicAwards.${index}.document`] ? 'border-red-500' : ''}`}
                                />
                                <p className="text-sm text-gray-500 mt-1">Upload certificate or proof of award (PDF, JPG, PNG max 2MB)</p>
                                {errors[`academicAwards.${index}.document`] && (
                                    <InputError message={errors[`academicAwards.${index}.document`]} className="mt-2" />
                                )}
                            </div>
                        </div>
                    </div>
                ))}

                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('academicAwards', {
                        title: '',
                        institution: '',
                        dateReceived: '',
                        description: '',
                        document: null
                    })}
                >
                    Add Academic Award
                </PrimaryButton>
            </section>

            {/* Community Awards */}
            <section className="space-y-4">
                <h2 className="text-xl font-bold">Community Service Awards</h2>
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Award Conferred
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name & Address of Conferring Organization
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date Awarded
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {formData.communityAwards?.map((award, index) => (
                                <tr key={index}>
                                    <td className="px-6 py-4">
                                        <TextInput
                                            value={award.title}
                                            className={`block w-full ${errors[`communityAwards.${index}.title`] ? 'border-red-500' : ''}`}
                                            onChange={(e) => handleArrayFieldChange('communityAwards', index, 'title', e.target.value)}
                                        />
                                        {errors[`communityAwards.${index}.title`] && (
                                            <InputError message={errors[`communityAwards.${index}.title`]} className="mt-2" />
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        <TextInput
                                            value={award.organization}
                                            className={`block w-full ${errors[`communityAwards.${index}.organization`] ? 'border-red-500' : ''}`}
                                            onChange={(e) => handleArrayFieldChange('communityAwards', index, 'organization', e.target.value)}
                                        />
                                        {errors[`communityAwards.${index}.organization`] && (
                                            <InputError message={errors[`communityAwards.${index}.organization`]} className="mt-2" />
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        <TextInput
                                            type="date"
                                            value={award.dateAwarded}
                                            className="block w-full"
                                            onChange={(e) => handleArrayFieldChange('communityAwards', index, 'dateAwarded', e.target.value)}
                                        />
                                        {errors[`communityAwards.${index}.dateAwarded`] && (
                                            <InputError message={errors[`communityAwards.${index}.dateAwarded`]} className="mt-2" />
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        {formData.communityAwards.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('communityAwards', index)}
                                                className="text-red-600 hover:text-red-800"
                                            >
                                                Remove
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('communityAwards', {
                        title: '',
                        organization: '',
                        dateAwarded: ''
                    })}
                >
                    Add Community Award
                </PrimaryButton>
            </section>

            {/* Work-Related Awards */}
            <section className="space-y-4">
                <h2 className="text-xl font-bold">Work-Related Awards</h2>
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Award Conferred
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name & Address of Conferring Organization
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date Awarded
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {formData.workAwards?.map((award, index) => (
                                <tr key={index}>
                                    <td className="px-6 py-4">
                                        <TextInput
                                            value={award.title}
                                            className={`block w-full ${errors[`workAwards.${index}.title`] ? 'border-red-500' : ''}`}
                                            onChange={(e) => handleArrayFieldChange('workAwards', index, 'title', e.target.value)}
                                        />
                                        {errors[`workAwards.${index}.title`] && (
                                            <InputError message={errors[`workAwards.${index}.title`]} className="mt-2" />
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        <TextInput
                                            value={award.organization}
                                            className={`block w-full ${errors[`workAwards.${index}.organization`] ? 'border-red-500' : ''}`}
                                            onChange={(e) => handleArrayFieldChange('workAwards', index, 'organization', e.target.value)}
                                        />
                                        {errors[`workAwards.${index}.organization`] && (
                                            <InputError message={errors[`workAwards.${index}.organization`]} className="mt-2" />
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        <TextInput
                                            type="date"
                                            value={award.dateAwarded}
                                            className="block w-full"
                                            onChange={(e) => handleArrayFieldChange('workAwards', index, 'dateAwarded', e.target.value)}
                                        />
                                        {errors[`workAwards.${index}.dateAwarded`] && (
                                            <InputError message={errors[`workAwards.${index}.dateAwarded`]} className="mt-2" />
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        {formData.workAwards.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('workAwards', index)}
                                                className="text-red-600 hover:text-red-800"
                                            >
                                                Remove
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('workAwards', {
                        title: '',
                        organization: '',
                        dateAwarded: ''
                    })}
                >
                    Add Work Award
                </PrimaryButton>
            </section>
        </div>
    );
}
