import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import TextArea from '@/Components/TextArea';

export default function LearningObjectivesStep({ formData, errors, handleInputChange }) {
    return (
        <div className="space-y-6">
            {/* Degree Program Preferences */}
            <div className="space-y-4">
                <InputLabel value="What degree program or field are you applying for?" required />

                <div>
                    <InputLabel htmlFor="firstPriority" value="First Priority" className="text-sm" />
                    <TextInput
                        id="firstPriority"
                        name="firstPriority"
                        value={formData.firstPriority}
                        className={`mt-1 block w-full ${errors.firstPriority ? 'error-field border-red-500' : ''}`}
                        onChange={handleInputChange}
                    />
                    {errors.firstPriority && (
                        <InputError message={errors.firstPriority} className="mt-2" />
                    )}
                </div>

                <div>
                    <InputLabel htmlFor="secondPriority" value="Second Priority" className="text-sm" />
                    <TextInput
                        id="secondPriority"
                        name="secondPriority"
                        value={formData.secondPriority}
                        className={`mt-1 block w-full ${errors.secondPriority ? 'error-field border-red-500' : ''}`}
                        onChange={handleInputChange}
                    />
                    {errors.secondPriority && (
                        <InputError message={errors.secondPriority} className="mt-2" />
                    )}
                </div>

                <div>
                    <InputLabel htmlFor="thirdPriority" value="Third Priority" className="text-sm" />
                    <TextInput
                        id="thirdPriority"
                        name="thirdPriority"
                        value={formData.thirdPriority}
                        className={`mt-1 block w-full ${errors.thirdPriority ? 'error-field border-red-500' : ''}`}
                        onChange={handleInputChange}
                    />
                    {errors.thirdPriority && (
                        <InputError message={errors.thirdPriority} className="mt-2" />
                    )}
                </div>
            </div>

            {/* Goal Statement */}
            <div>
                <InputLabel htmlFor="goalStatement" value="What are your goals, objectives, or purposes for applying for this degree?" required />
                <TextArea
                    id="goalStatement"
                    name="goalStatement"
                    value={formData.goalStatement}
                    className={`mt-1 block w-full ${errors.goalStatement ? 'error-field border-red-500' : ''}`}
                    onChange={handleInputChange}
                    rows={4}
                />
                {errors.goalStatement && (
                    <InputError message={errors.goalStatement} className="mt-2" />
                )}
            </div>

            {/* Time Commitment */}
            <div>
                <InputLabel value="How much time can you commit to your studies?" required />
                <div className={`mt-2 space-y-2 ${errors.timeCommitment ? 'error-field' : ''}`}>
                    {[
                        'Full-time',
                        'Part-time',
                        'Weekends only',
                        'Evenings only',
                        'Others'
                    ].map((option) => (
                        <label key={option} className="flex items-center">
                            <input
                                type="radio"
                                name="timeCommitment"
                                value={option}
                                checked={formData.timeCommitment === option}
                                onChange={handleInputChange}
                                className={`mr-2 ${errors.timeCommitment ? 'border-red-500' : ''}`}
                            />
                            {option}
                        </label>
                    ))}
                </div>
                {errors.timeCommitment && (
                    <InputError message={errors.timeCommitment} className="mt-2" />
                )}

                {formData.timeCommitment === 'Others' && (
                    <div className="mt-2">
                        <TextInput
                            id="otherTimeCommitment"
                            name="otherTimeCommitment"
                            value={formData.otherTimeCommitment}
                            className={`mt-1 block w-full ${errors.otherTimeCommitment ? 'error-field border-red-500' : ''}`}
                            placeholder="Please specify"
                            onChange={handleInputChange}
                        />
                        {errors.otherTimeCommitment && (
                            <InputError message={errors.otherTimeCommitment} className="mt-2" />
                        )}
                    </div>
                )}
            </div>

            {/* Cost Payment */}
            <div>
                <InputLabel value="How do you plan to pay for the cost of your studies?" required />
                <div className={`mt-2 space-y-2 ${errors.costPayment ? 'error-field' : ''}`}>
                    {[
                        'Self-funded',
                        'Family support',
                        'Employer-sponsored',
                        'Scholarship',
                        'Student loan',
                        'Others'
                    ].map((option) => (
                        <label key={option} className="flex items-center">
                            <input
                                type="radio"
                                name="costPayment"
                                value={option}
                                checked={formData.costPayment === option}
                                onChange={handleInputChange}
                                className={`mr-2 ${errors.costPayment ? 'border-red-500' : ''}`}
                            />
                            {option}
                        </label>
                    ))}
                </div>
                {errors.costPayment && (
                    <InputError message={errors.costPayment} className="mt-2" />
                )}

                {formData.costPayment === 'Others' && (
                    <div className="mt-2">
                        <TextInput
                            id="otherCostPayment"
                            name="otherCostPayment"
                            value={formData.otherCostPayment}
                            className={`mt-1 block w-full ${errors.otherCostPayment ? 'error-field border-red-500' : ''}`}
                            placeholder="Please specify"
                            onChange={handleInputChange}
                        />
                        {errors.otherCostPayment && (
                            <InputError message={errors.otherCostPayment} className="mt-2" />
                        )}
                    </div>
                )}
            </div>

            {/* Completion Timeline */}
            <div>
                <InputLabel value="How soon do you need to complete accreditation/equivalency?" required />
                <div className={`mt-2 space-y-2 ${errors.completionTimeline ? 'error-field' : ''}`}>
                    {[
                        'Less than 1 year',
                        '1 year',
                        '2 years',
                        '3 years',
                        '4 years',
                        'More than 5 years'
                    ].map((timeline) => (
                        <label key={timeline} className="flex items-center">
                            <input
                                type="radio"
                                name="completionTimeline"
                                value={timeline}
                                checked={formData.completionTimeline === timeline}
                                onChange={handleInputChange}
                                className={`mr-2 ${errors.completionTimeline ? 'border-red-500' : ''}`}
                            />
                            {timeline}
                        </label>
                    ))}
                </div>
                {errors.completionTimeline && (
                    <InputError message={errors.completionTimeline} className="mt-2" />
                )}
            </div>
        </div>
    );
}
