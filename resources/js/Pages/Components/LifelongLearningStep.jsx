import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextArea from '@/Components/TextArea';
import PrimaryButton from '@/Components/PrimaryButton';

export default function LifelongLearningStep({ 
    formData, 
    errors, 
    handleInputChange,
    handleArrayFieldChange,
    addArrayItem,
    removeArrayItem 
}) {
    return (
        <div className="space-y-8">
            <div>
                <h2 className="text-xl font-bold mb-4">VI. LIFELONG LEARNING EXPERIENCES</h2>
                <p className="text-sm text-gray-600 mb-6">
                    In this section, please indicate the various life experiences from which you must have 
                    derived some learning experience. Please indicate here unpaid volunteer work.
                </p>
            </div>

            {/* A. Hobbies/Leisure Activities */}
            <section className="space-y-4">
                <h3 className="text-lg font-semibold">A. Hobbies / Leisure Activities</h3>
                <p className="text-sm text-gray-600">
                    Include only those leisure activities which can be considered learning opportunities. 
                    Activities involving skill ratings for competition may also indicate your level for ease in evaluation.
                </p>
                
                {formData.hobbies.map((hobby, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <TextArea
                            value={hobby.description}
                            className={`mt-1 block w-full ${errors[`hobbies.${index}.description`] ? 'border-red-500' : ''}`}
                            rows={3}
                            placeholder="Describe your hobby and its learning value..."
                            onChange={(e) => handleArrayFieldChange('hobbies', index, 'description', e.target.value)}
                        />
                        {errors[`hobbies.${index}.description`] && (
                            <InputError message={errors[`hobbies.${index}.description`]} className="mt-2" />
                        )}
                        {formData.hobbies.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('hobbies', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Hobby
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('hobbies', { description: '' })}
                >
                    Add Hobby
                </PrimaryButton>
            </section>

            {/* B. Special Skills */}
            <section className="space-y-4">
                <h3 className="text-lg font-semibold">B. Special Skills</h3>
                <p className="text-sm text-gray-600">
                    Note down those special skills which you think must be related to the field of study 
                    you want to pursue. These can be considered as substitutes for specific credit requirements.
                </p>
                
                {formData.specialSkills.map((skill, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <TextArea
                            value={skill.description}
                            className={`mt-1 block w-full ${errors[`specialSkills.${index}.description`] ? 'border-red-500' : ''}`}
                            rows={3}
                            placeholder="Describe your special skill and its relevance..."
                            onChange={(e) => handleArrayFieldChange('specialSkills', index, 'description', e.target.value)}
                        />
                        {errors[`specialSkills.${index}.description`] && (
                            <InputError message={errors[`specialSkills.${index}.description`]} className="mt-2" />
                        )}
                        {formData.specialSkills.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('specialSkills', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Skill
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('specialSkills', { description: '' })}
                >
                    Add Special Skill
                </PrimaryButton>
            </section>

            {/* C. Work-Related Activities */}
            <section className="space-y-4">
                <h3 className="text-lg font-semibold">C. Work-Related Activities</h3>
                <p className="text-sm text-gray-600">
                    Identify tasks that required new skills and knowledge beyond your usual job description. 
                    Include on-the-job training or apprenticeship experiences.
                </p>
                
                {formData.workActivities.map((activity, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <TextArea
                            value={activity.description}
                            className={`mt-1 block w-full ${errors[`workActivities.${index}.description`] ? 'border-red-500' : ''}`}
                            rows={4}
                            placeholder="Describe the activity and what new skills or knowledge you gained..."
                            onChange={(e) => handleArrayFieldChange('workActivities', index, 'description', e.target.value)}
                        />
                        {errors[`workActivities.${index}.description`] && (
                            <InputError message={errors[`workActivities.${index}.description`]} className="mt-2" />
                        )}
                        {formData.workActivities.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('workActivities', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Activity
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('workActivities', { description: '' })}
                >
                    Add Work Activity
                </PrimaryButton>
            </section>

            {/* D. Volunteer Activities */}
            <section className="space-y-4">
                <h3 className="text-lg font-semibold">D. Volunteer Activities</h3>
                <p className="text-sm text-gray-600">
                    Include volunteer activities that demonstrate learning opportunities related to your course. 
                    Activities should be validatable through accredited organizations.
                </p>
                
                {formData.volunteerActivities.map((activity, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <TextArea
                            value={activity.description}
                            className={`mt-1 block w-full ${errors[`volunteerActivities.${index}.description`] ? 'border-red-500' : ''}`}
                            rows={4}
                            placeholder="Describe your volunteer activity and its learning value..."
                            onChange={(e) => handleArrayFieldChange('volunteerActivities', index, 'description', e.target.value)}
                        />
                        {errors[`volunteerActivities.${index}.description`] && (
                            <InputError message={errors[`volunteerActivities.${index}.description`]} className="mt-2" />
                        )}
                        {formData.volunteerActivities.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('volunteerActivities', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Activity
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('volunteerActivities', { description: '' })}
                >
                    Add Volunteer Activity
                </PrimaryButton>
            </section>

            {/* E. Travels */}
            <section className="space-y-4">
                <h3 className="text-lg font-semibold">E. Travels</h3>
                <p className="text-sm text-gray-600">
                    Enumerate places visited within and outside the Philippines. Include the nature of travel 
                    and what new learning experiences were obtained.
                </p>
                
                {formData.travels.map((travel, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <TextArea
                            value={travel.description}
                            className={`mt-1 block w-full ${errors[`travels.${index}.description`] ? 'border-red-500' : ''}`}
                            rows={4}
                            placeholder="Describe your travel experience, purpose, and learning outcomes..."
                            onChange={(e) => handleArrayFieldChange('travels', index, 'description', e.target.value)}
                        />
                        {errors[`travels.${index}.description`] && (
                            <InputError message={errors[`travels.${index}.description`]} className="mt-2" />
                        )}
                        {formData.travels.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('travels', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Travel
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('travels', { description: '' })}
                >
                    Add Travel Experience
                </PrimaryButton>
            </section>
        </div>
    );
} 