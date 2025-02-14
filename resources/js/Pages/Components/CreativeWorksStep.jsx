import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import TextArea from '@/Components/TextArea';
import PrimaryButton from '@/Components/PrimaryButton';

export default function CreativeWorksStep({
    formData,
    errors,
    handleArrayFieldChange,
    addArrayItem,
    removeArrayItem
}) {
    // Add debug logs
    console.log('CreativeWorksStep errors:', errors);

    return (
        <div className="space-y-8">
            <div>
                <h2 className="text-xl font-bold mb-4">V. CREATIVE WORKS AND SPECIAL ACCOMPLISHMENTS</h2>
                <p className="text-sm text-gray-600 mb-6">
                    In this section, enumerate the various creative works you have accomplished and other special accomplishments.
                    Examples include inventions, published and unpublished literary works, musical work, visual and performing arts,
                    exceptional sports achievements, social, cultural and leisure activities, etc.
                </p>
                <p className="text-sm text-gray-600 mb-6">
                    Please provide a detailed description of your work/accomplishments and explain why it qualifies as special.
                    For example, cooking regular meals will not qualify; however, demonstrating expertise in preparing regional
                    delicacies shows sophisticated understanding of cultural cuisine.
                </p>
            </div>

            {formData.creativeWorks.map((work, index) => (
                <div key={index} className="border p-6 rounded-lg space-y-6 bg-white shadow-sm">
                    {/* Title of Work/Accomplishment */}
                    <div>
                        <InputLabel
                            value="Title or Name of Work/Accomplishment"
                            className="font-semibold"
                        />
                        <TextInput
                            value={work.title}
                            className="mt-1 block w-full"
                            onChange={(e) => handleArrayFieldChange('creativeWorks', index, 'title', e.target.value)}
                        />
                        {errors[`creativeWorks.${index}.title`] && (
                            <InputError message={errors[`creativeWorks.${index}.title`]} className="mt-2" />
                        )}
                    </div>

                    {/* Description */}
                    <div>
                        <InputLabel
                            value="Detailed Description"
                            className="font-semibold"
                        />
                        <TextArea
                            value={work.description}
                            className="mt-1 block w-full"
                            rows={4}
                            placeholder="Provide a detailed description of your work/accomplishment..."
                            onChange={(e) => handleArrayFieldChange('creativeWorks', index, 'description', e.target.value)}
                        />
                        {errors[`creativeWorks.${index}.description`] && (
                            <InputError message={errors[`creativeWorks.${index}.description`]} className="mt-2" />
                        )}
                    </div>

                    {/* Significance */}
                    <div>
                        <InputLabel
                            value="Why is this a Special Accomplishment?"
                            className="font-semibold"
                        />
                        <TextArea
                            value={work.significance}
                            className="mt-1 block w-full"
                            rows={3}
                            placeholder="Explain why this qualifies as a special accomplishment..."
                            onChange={(e) => handleArrayFieldChange('creativeWorks', index, 'significance', e.target.value)}
                        />
                        {errors[`creativeWorks.${index}.significance`] && (
                            <InputError message={errors[`creativeWorks.${index}.significance`]} className="mt-2" />
                        )}
                    </div>

                    {/* Date Completed */}
                    <div>
                        <InputLabel
                            value="Date Completed"
                            className="font-semibold"
                        />
                        <TextInput
                            type="date"
                            value={work.dateCompleted}
                            className="mt-1 block w-full"
                            onChange={(e) => handleArrayFieldChange('creativeWorks', index, 'dateCompleted', e.target.value)}
                        />
                        {errors[`creativeWorks.${index}.dateCompleted`] && (
                            <InputError message={errors[`creativeWorks.${index}.dateCompleted`]} className="mt-2" />
                        )}
                    </div>

                    {/* Corroborating Institution */}
                    <div>
                        <InputLabel
                            value="Corroborating Institution or Body"
                            className="font-semibold"
                        />
                        <TextInput
                            value={work.corroboratingBody}
                            className="mt-1 block w-full"
                            placeholder="Name of institution that can verify this accomplishment"
                            onChange={(e) => handleArrayFieldChange('creativeWorks', index, 'corroboratingBody', e.target.value)}
                        />
                        {errors[`creativeWorks.${index}.corroboratingBody`] && (
                            <InputError message={errors[`creativeWorks.${index}.corroboratingBody`]} className="mt-2" />
                        )}
                    </div>

                    {/* Remove Button */}
                    {formData.creativeWorks.length > 1 && (
                        <div className="pt-4">
                            <button
                                type="button"
                                onClick={() => removeArrayItem('creativeWorks', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Entry
                            </button>
                        </div>
                    )}
                </div>
            ))}

            {/* Add Button */}
            <div>
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('creativeWorks', {
                        title: '',
                        description: '',
                        significance: '',
                        dateCompleted: '',
                        corroboratingBody: ''
                    })}
                >
                    Add Another Creative Work/Accomplishment
                </PrimaryButton>
            </div>
        </div>
    );
}
