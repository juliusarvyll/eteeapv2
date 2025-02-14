import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function EducationStep({
    formData,
    errors,
    handleInputChange,
    handleArrayFieldChange,
    addArrayItem,
    removeArrayItem
}) {
    return (
        <div className="space-y-8">
            {/* A. Formal Education */}
            <div className="space-y-6">
                <h2 className="text-xl font-bold">A. Formal Education</h2>

                {/* 1. Elementary */}
                <section className="space-y-4">
                    <h3 className="text-lg font-semibold">1. Elementary</h3>

                    <div className="space-y-4">
                        <p className="text-sm text-gray-600">1.1 What is the name and address of the elementary school you attended?</p>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel htmlFor="elementarySchool" value="Name of School" required />
                                <TextInput
                                    id="elementarySchool"
                                    name="elementarySchool"
                                    value={formData.elementarySchool}
                                    className={`mt-1 block w-full ${errors.elementarySchool ? 'border-red-500' : ''}`}
                                    onChange={handleInputChange}
                                />
                                {errors.elementarySchool && (
                                    <InputError message={errors.elementarySchool[0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel htmlFor="elementaryAddress" value="Address" required />
                                <TextInput
                                    id="elementaryAddress"
                                    name="elementaryAddress"
                                    value={formData.elementaryAddress}
                                    className={`mt-1 block w-full ${errors.elementaryAddress ? 'border-red-500' : ''}`}
                                    onChange={handleInputChange}
                                />
                                {errors.elementaryAddress && (
                                    <InputError message={errors.elementaryAddress[0]} className="mt-2" />
                                )}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel htmlFor="elementaryDateFrom" value="Year Started" required />
                                <TextInput
                                    type="number"
                                    id="elementaryDateFrom"
                                    name="elementaryDateFrom"
                                    value={formData.elementaryDateFrom || ''}
                                    min="1900"
                                    max={new Date().getFullYear()}
                                    placeholder="YYYY"
                                    className={`mt-1 block w-full ${errors.elementaryDateFrom ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleInputChange({
                                        target: {
                                            name: e.target.name,
                                            value: parseInt(e.target.value) || ''
                                        }
                                    })}
                                />
                                {errors.elementaryDateFrom && (
                                    <InputError message={errors.elementaryDateFrom[0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel htmlFor="elementaryDateTo" value="Year Completed" required />
                                <TextInput
                                    type="number"
                                    id="elementaryDateTo"
                                    name="elementaryDateTo"
                                    value={formData.elementaryDateTo}
                                    min="1900"
                                    max={new Date().getFullYear()}
                                    placeholder="YYYY"
                                    className={`mt-1 block w-full ${errors.elementaryDateTo ? 'border-red-500' : ''}`}
                                    onChange={handleInputChange}
                                />
                                {errors.elementaryDateTo && (
                                    <InputError message={errors.elementaryDateTo[0]} className="mt-2" />
                                )}
                            </div>
                        </div>

                        <div>
                            <div className="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    id="hasElementaryDiploma"
                                    name="hasElementaryDiploma"
                                    checked={Boolean(formData.hasElementaryDiploma)}
                                    onChange={(e) => {
                                        handleInputChange({
                                            target: {
                                                name: 'hasElementaryDiploma',
                                                value: e.target.checked ? '1' : '0',
                                                type: 'checkbox',
                                                checked: e.target.checked
                                            }
                                        });
                                    }}
                                    className={`${errors.hasElementaryDiploma ? 'border-red-500' : ''}`}
                                />
                                <InputLabel htmlFor="hasElementaryDiploma" value="Do you have your elementary school diploma?" required />
                            </div>
                            {errors.hasElementaryDiploma && (
                                <InputError message={errors.hasElementaryDiploma[0]} className="mt-2" />
                            )}
                        </div>

                        {formData.hasElementaryDiploma && (
                            <div className="mt-4">
                                <InputLabel htmlFor="elementaryDiplomaFile" value="Upload Elementary Diploma" required />
                                <input
                                    type="file"
                                    id="elementaryDiplomaFile"
                                    name="elementaryDiplomaFile"
                                    onChange={(e) => {
                                        handleInputChange({
                                            target: {
                                                name: 'elementaryDiplomaFile',
                                                value: e.target.files[0]
                                            }
                                        });
                                    }}
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    className={`mt-1 block w-full ${errors.elementaryDiplomaFile ? 'border-red-500' : ''}`}
                                />
                                <p className="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (max 2MB)</p>
                                {errors.elementaryDiplomaFile && (
                                    <InputError message={errors.elementaryDiplomaFile[0]} className="mt-2" />
                                )}
                            </div>
                        )}
                    </div>
                </section>

                {/* 2. High School and PEPT Selection */}
                <section className="space-y-4">
                    <h3 className="text-lg font-semibold">2. Secondary Education</h3>

                    <div className="space-y-4">
                        <p className="text-sm text-gray-600">How did you complete your secondary education?</p>
                        <div className="space-y-2">
                            <label className="flex items-center space-x-2">
                                <input
                                    type="radio"
                                    name="secondaryEducationType"
                                    value="regular"
                                    checked={!formData.hasPEPT}
                                    onChange={(e) => {
                                        handleInputChange({
                                            target: {
                                                name: 'hasPEPT',
                                                value: '0',
                                                type: 'checkbox',
                                                checked: false
                                            }
                                        });
                                    }}
                                    className="mr-2"
                                />
                                <span>Regular High School</span>
                            </label>

                            <label className="flex items-center space-x-2">
                                <input
                                    type="radio"
                                    name="secondaryEducationType"
                                    value="pept"
                                    checked={formData.hasPEPT}
                                    onChange={(e) => {
                                        handleInputChange({
                                            target: {
                                                name: 'hasPEPT',
                                                value: '1',
                                                type: 'checkbox',
                                                checked: true
                                            }
                                        });
                                        if (!e.target.checked) {
                                            handleInputChange({
                                                target: {
                                                    name: 'peptYear',
                                                    value: ''
                                                }
                                            });
                                            handleInputChange({
                                                target: {
                                                    name: 'peptGrade',
                                                    value: ''
                                                }
                                            });
                                        }
                                    }}
                                    className="mr-2"
                                />
                                <span>Philippine Educational Placement Test (PEPT)</span>
                            </label>
                        </div>

                        {/* Show High School form if regular education was selected */}
                        {!formData.hasPEPT && (
                            <div className="mt-4">
                                <p className="text-sm text-gray-600">2.1 Provide your high school information:</p>
                                {formData.highSchools.map((school, index) => (
                                    <div key={index} className="border p-4 rounded-lg space-y-4">
                                        <div className="flex justify-between items-center">
                                            <h4 className="font-medium">High School {index + 1}</h4>
                                            {index > 0 && (
                                                <button
                                                    type="button"
                                                    onClick={() => removeArrayItem('highSchools', index)}
                                                    className="text-red-600 hover:text-red-800"
                                                >
                                                    Remove
                                                </button>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel value="Name of School" required />
                                                <TextInput
                                                    name={`highSchools.${index}.name`}
                                                    value={school.name}
                                                    onChange={(e) => handleArrayFieldChange('highSchools', index, 'name', e.target.value)}
                                                    className={`mt-1 block w-full ${errors[`highSchools.${index}.name`] ? 'border-red-500' : ''}`}
                                                />
                                                {errors[`highSchools.${index}.name`] && (
                                                    <InputError message={errors[`highSchools.${index}.name`][0]} className="mt-2" />
                                                )}
                                            </div>
                                            <div>
                                                <InputLabel value="Address" required />
                                                <TextInput
                                                    name={`highSchools.${index}.address`}
                                                    value={school.address}
                                                    onChange={(e) => handleArrayFieldChange('highSchools', index, 'address', e.target.value)}
                                                    className={`mt-1 block w-full ${errors[`highSchools.${index}.address`] ? 'border-red-500' : ''}`}
                                                />
                                                {errors[`highSchools.${index}.address`] && (
                                                    <InputError message={errors[`highSchools.${index}.address`][0]} className="mt-2" />
                                                )}
                                            </div>
                                            <div>
                                                <InputLabel value="Type of School" required />
                                                <select
                                                    name="type"
                                                    value={school.type}
                                                    onChange={(e) => handleArrayFieldChange('highSchools', index, 'type', e.target.value)}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                    <option value="">Select Type</option>
                                                    <option value="Junior High School">Junior High School</option>
                                                    <option value="Senior High School">Senior High School</option>
                                                </select>
                                            </div>

                                            {/* Show strand field only for Senior High School */}
                                            {school.type === 'Senior High School' && (
                                                <div>
                                                    <InputLabel value="Strand" required />
                                                    <select
                                                        name="strand"
                                                        value={school.strand}
                                                        onChange={(e) => handleArrayFieldChange('highSchools', index, 'strand', e.target.value)}
                                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    >
                                                        <option value="">Select Strand</option>
                                                        <option value="STEM">STEM (Science, Technology, Engineering, and Mathematics)</option>
                                                        <option value="HUMSS">HUMSS (Humanities and Social Sciences)</option>
                                                        <option value="ABM">ABM (Accountancy, Business, and Management)</option>
                                                        <option value="GAS">GAS (General Academic Strand)</option>
                                                        <option value="TVL">TVL (Technical-Vocational-Livelihood)</option>
                                                        <option value="Sports">Sports Track</option>
                                                        <option value="Arts and Design">Arts and Design Track</option>
                                                    </select>
                                                </div>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel value="Year Started" required />
                                                <TextInput
                                                    type="number"
                                                    name="dateFrom"
                                                    value={school.dateFrom || ''}
                                                    min="1900"
                                                    max={new Date().getFullYear()}
                                                    placeholder="YYYY"
                                                    onChange={(e) => handleArrayFieldChange('highSchools', index, 'dateFrom', parseInt(e.target.value) || '')}
                                                    className={`mt-1 block w-full ${errors[`highSchools.${index}.dateFrom`] ? 'border-red-500' : ''}`}
                                                />
                                                {errors[`highSchools.${index}.dateFrom`] && (
                                                    <InputError message={errors[`highSchools.${index}.dateFrom`][0]} className="mt-2" />
                                                )}
                                            </div>
                                            <div>
                                                <InputLabel value="Year Completed" required />
                                                <TextInput
                                                    type="number"
                                                    name="dateTo"
                                                    value={school.dateTo}
                                                    min="1900"
                                                    max={new Date().getFullYear()}
                                                    placeholder="YYYY"
                                                    onChange={(e) => handleArrayFieldChange('highSchools', index, 'dateTo', parseInt(e.target.value) || '')}
                                                    className={`mt-1 block w-full ${errors[`highSchools.${index}.dateTo`] ? 'border-red-500' : ''}`}
                                                />
                                                {errors[`highSchools.${index}.dateTo`] && (
                                                    <InputError message={errors[`highSchools.${index}.dateTo`][0]} className="mt-2" />
                                                )}
                                            </div>
                                        </div>
                                        <div>
                                            <div className="flex items-center space-x-2">
                                                <input
                                                    type="checkbox"
                                                    id="hasHighSchoolDiploma"
                                                    name="hasHighSchoolDiploma"
                                                    checked={Boolean(formData.hasHighSchoolDiploma)}
                                                    onChange={(e) => {
                                                        handleInputChange({
                                                            target: {
                                                                name: 'hasHighSchoolDiploma',
                                                                value: e.target.checked ? '1' : '0',
                                                                type: 'checkbox',
                                                                checked: e.target.checked
                                                            }
                                                        });
                                                    }}
                                                    className={`${errors.hasHighSchoolDiploma ? 'border-red-500' : ''}`}
                                                />
                                                <InputLabel htmlFor="hasHighSchoolDiploma" value="Do you have your high school diploma?" required />
                                            </div>
                                            {errors.hasHighSchoolDiploma && (
                                                <InputError message={errors.hasHighSchoolDiploma[0]} className="mt-2" />
                                            )}
                                        </div>

                                        {formData.hasHighSchoolDiploma && (
                                            <div className="mt-4">
                                                <InputLabel htmlFor="highSchoolDiplomaFile" value="Upload High School Diploma" required />
                                                <input
                                                    type="file"
                                                    id="highSchoolDiplomaFile"
                                                    name="highSchoolDiplomaFile"
                                                    onChange={(e) => {
                                                        handleInputChange({
                                                            target: {
                                                                name: 'highSchoolDiplomaFile',
                                                                value: e.target.files[0]
                                                            }
                                                        });
                                                    }}
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    className={`mt-1 block w-full ${errors.highSchoolDiplomaFile ? 'border-red-500' : ''}`}
                                                />
                                                <p className="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (max 2MB)</p>
                                                {errors.highSchoolDiplomaFile && (
                                                    <InputError message={errors.highSchoolDiplomaFile[0]} className="mt-2" />
                                                )}
                                            </div>
                                        )}
                                    </div>
                                ))}
                                <PrimaryButton
                                    type="button"
                                    onClick={() => addArrayItem('highSchools', {
                                        name: '',
                                        address: '',
                                        type: '',
                                        dateFrom: '',
                                        dateTo: '',
                                        strand: ''
                                    })}
                                >
                                    Add Another High School
                                </PrimaryButton>
                            </div>
                        )}

                        {/* Show PEPT form if PEPT was selected */}
                        {formData.hasPEPT && (
                            <div className="mt-4 space-y-4">
                                <div>
                                    <p className="text-sm text-gray-600">2.1 When was your PEPT certification issued?</p>
                                    <div className="mt-2">
                                        <InputLabel htmlFor="peptYear" value="Year" required />
                                        <TextInput
                                            id="peptYear"
                                            name="peptYear"
                                            type="number"
                                            value={formData.peptYear || ''}
                                            min="1900"
                                            max={new Date().getFullYear()}
                                            placeholder="YYYY"
                                            className={`mt-1 block w-full ${errors.peptYear ? 'border-red-500' : ''}`}
                                            onChange={(e) => handleInputChange({
                                                target: {
                                                    name: e.target.name,
                                                    value: parseInt(e.target.value) || ''
                                                }
                                            })}
                                        />
                                        {errors.peptYear && (
                                            <InputError message={errors.peptYear[0]} className="mt-2" />
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <p className="text-sm text-gray-600">2.2 What was your placement grade or year level?</p>
                                    <div className="mt-2">
                                        <InputLabel htmlFor="peptGrade" value="Grade/Year" required />
                                        <TextInput
                                            id="peptGrade"
                                            name="peptGrade"
                                            value={formData.peptGrade}
                                            className={`mt-1 block w-full ${errors.peptGrade ? 'border-red-500' : ''}`}
                                            onChange={handleInputChange}
                                        />
                                        {errors.peptGrade && (
                                            <InputError message={errors.peptGrade[0]} className="mt-2" />
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="peptCertificateFile" value="Upload PEPT Certificate" required />
                                    <input
                                        type="file"
                                        id="peptCertificateFile"
                                        name="peptCertificateFile"
                                        onChange={(e) => {
                                            handleInputChange({
                                                target: {
                                                    name: 'peptCertificateFile',
                                                    value: e.target.files[0]
                                                }
                                            });
                                        }}
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        className={`mt-1 block w-full ${errors.peptCertificateFile ? 'border-red-500' : ''}`}
                                    />
                                    <p className="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (max 2MB)</p>
                                    {errors.peptCertificateFile && (
                                        <InputError message={errors.peptCertificateFile[0]} className="mt-2" />
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </section>

                {/* 3. Post-Secondary */}
                <section className="space-y-4">
                    <h3 className="text-lg font-semibold">4. Post-Secondary Formal Education</h3>

                    <div>
                        <p className="text-sm text-gray-600">4.1 Do you have any post-secondary diplomas or degrees?</p>
                        <div className="mt-2">
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    name="hasPostSecondaryDiploma"
                                    checked={Boolean(formData.hasPostSecondaryDiploma)}
                                    onChange={(e) => {
                                        handleInputChange({
                                            target: {
                                                name: 'hasPostSecondaryDiploma',
                                                value: e.target.checked ? '1' : '0',
                                                type: 'checkbox',
                                                checked: e.target.checked
                                            }
                                        });
                                    }}
                                    className="mr-2"
                                />
                                Yes
                            </label>
                        </div>
                    </div>
                    {formData.hasPostSecondaryDiploma && (
                        <div className="mt-4">
                            <InputLabel htmlFor="postSecondaryDiplomaFile" value="Upload Post-Secondary Diploma(s)" />
                            <input
                                type="file"
                                id="postSecondaryDiplomaFile"
                                name="postSecondaryDiplomaFile"
                                onChange={(e) => {
                                    handleInputChange({
                                        target: {
                                            name: 'postSecondaryDiplomaFile',
                                            value: e.target.files[0]
                                        }
                                    });
                                }}
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png"
                                className="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100"
                            />
                            <p className="mt-1 text-sm text-gray-500">You can select multiple files if you have more than one diploma</p>
                            {errors.postSecondaryDiplomaFile && (
                                <InputError message={errors.postSecondaryDiplomaFile[0]} className="mt-2" />
                            )}
                        </div>
                    )}

                    <p className="text-sm text-gray-600">4.2 Provide details about any post-secondary courses or degree programs:</p>

                    {formData.postSecondary.map((education, index) => (
                        <div key={index} className="border p-4 rounded-lg space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel value="Course/Degree Program" />
                                    <TextInput
                                        name={`postSecondary.${index}.program`}
                                        value={education.program}
                                        className={`mt-1 block w-full ${errors[`postSecondary.${index}.program`] ? 'border-red-500' : ''}`}
                                        onChange={(e) => handleArrayFieldChange('postSecondary', index, 'program', e.target.value)}
                                    />
                                    {errors[`postSecondary.${index}.program`] && (
                                        <InputError message={errors[`postSecondary.${index}.program`][0]} className="mt-2" />
                                    )}
                                </div>
                                <div>
                                    <InputLabel value="Name of Institution" />
                                    <TextInput
                                        name={`postSecondary.${index}.institution`}
                                        value={education.institution}
                                        className={`mt-1 block w-full ${errors[`postSecondary.${index}.institution`] ? 'border-red-500' : ''}`}
                                        onChange={(e) => handleArrayFieldChange('postSecondary', index, 'institution', e.target.value)}
                                    />
                                    {errors[`postSecondary.${index}.institution`] && (
                                        <InputError message={errors[`postSecondary.${index}.institution`][0]} className="mt-2" />
                                    )}
                                </div>
                                <div>
                                    <InputLabel value="School Year" />
                                    <TextInput
                                        type="number"
                                        name={`postSecondary.${index}.schoolYear`}
                                        value={education.schoolYear || ''}
                                        min="1900"
                                        max={new Date().getFullYear()}
                                        placeholder="YYYY"
                                        className={`mt-1 block w-full ${errors[`postSecondary.${index}.schoolYear`] ? 'border-red-500' : ''}`}
                                        onChange={(e) => handleArrayFieldChange('postSecondary', index, 'schoolYear', parseInt(e.target.value) || '')}
                                    />
                                    {errors[`postSecondary.${index}.schoolYear`] && (
                                        <InputError message={errors[`postSecondary.${index}.schoolYear`][0]} className="mt-2" />
                                    )}
                                </div>
                            </div>
                            {formData.postSecondary.length > 1 && (
                                <button
                                    type="button"
                                    onClick={() => removeArrayItem('postSecondary', index)}
                                    className="text-red-600 hover:text-red-800"
                                >
                                    Remove Entry
                                </button>
                            )}
                        </div>
                    ))}
                    <PrimaryButton
                        type="button"
                        onClick={() => addArrayItem('postSecondary', {
                            program: '',
                            institution: '',
                            schoolYear: ''
                        })}
                    >
                        Add Another Entry
                    </PrimaryButton>
                </section>
            </div>

            {/* B. Non-Formal Education */}
            <div className="space-y-6">
                <h2 className="text-xl font-bold">B. Non-Formal Education</h2>
                <p className="text-sm text-gray-600">
                    Non-formal education refers to short-term, structured training programs for specific purposes
                    such as skills development or values orientation.
                </p>

                {formData.nonFormalEducation.map((training, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Title of Training Program" />
                                <TextInput
                                    name={`nonFormalEducation.${index}.title`}
                                    value={training.title}
                                    className={`mt-1 block w-full ${errors[`nonFormalEducation.${index}.title`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('nonFormalEducation', index, 'title', e.target.value)}
                                />
                                {errors[`nonFormalEducation.${index}.title`] && (
                                    <InputError message={errors[`nonFormalEducation.${index}.title`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Sponsoring Organization" />
                                <TextInput
                                    name={`nonFormalEducation.${index}.organization`}
                                    value={training.organization}
                                    className={`mt-1 block w-full ${errors[`nonFormalEducation.${index}.organization`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('nonFormalEducation', index, 'organization', e.target.value)}
                                />
                                {errors[`nonFormalEducation.${index}.organization`] && (
                                    <InputError message={errors[`nonFormalEducation.${index}.organization`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Date" />
                                <TextInput
                                    type="date"
                                    name={`nonFormalEducation.${index}.date`}
                                    value={training.date}
                                    className={`mt-1 block w-full ${errors[`nonFormalEducation.${index}.date`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('nonFormalEducation', index, 'date', e.target.value)}
                                />
                                {errors[`nonFormalEducation.${index}.date`] && (
                                    <InputError message={errors[`nonFormalEducation.${index}.date`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Title of Certificate Obtained" />
                                <TextInput
                                    name={`nonFormalEducation.${index}.certificate`}
                                    value={training.certificate}
                                    className={`mt-1 block w-full ${errors[`nonFormalEducation.${index}.certificate`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('nonFormalEducation', index, 'certificate', e.target.value)}
                                />
                                {errors[`nonFormalEducation.${index}.certificate`] && (
                                    <InputError message={errors[`nonFormalEducation.${index}.certificate`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Nature of Participation" />
                                <TextInput
                                    name={`nonFormalEducation.${index}.participation`}
                                    value={training.participation}
                                    className={`mt-1 block w-full ${errors[`nonFormalEducation.${index}.participation`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('nonFormalEducation', index, 'participation', e.target.value)}
                                />
                                {errors[`nonFormalEducation.${index}.participation`] && (
                                    <InputError message={errors[`nonFormalEducation.${index}.participation`][0]} className="mt-2" />
                                )}
                            </div>
                        </div>
                        {formData.nonFormalEducation.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('nonFormalEducation', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Entry
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('nonFormalEducation', {
                        title: '',
                        organization: '',
                        date: '',
                        certificate: '',
                        participation: ''
                    })}
                >
                    Add Another Training Program
                </PrimaryButton>
            </div>

            {/* C. Other Certification Examinations */}
            <div className="space-y-6">
                <h2 className="text-xl font-bold">C. Other Certification Examinations</h2>
                <p className="text-sm text-gray-600">
                    Provide detailed information about certification exams taken for vocational or other skills.
                </p>

                {formData.certifications.map((cert, index) => (
                    <div key={index} className="border p-4 rounded-lg space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Title of Certification" />
                                <TextInput
                                    name={`certifications.${index}.title`}
                                    value={cert.title}
                                    className={`mt-1 block w-full ${errors[`certifications.${index}.title`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('certifications', index, 'title', e.target.value)}
                                />
                                {errors[`certifications.${index}.title`] && (
                                    <InputError message={errors[`certifications.${index}.title`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Name and Address of Certifying Agency" />
                                <TextInput
                                    name={`certifications.${index}.agency`}
                                    value={cert.agency}
                                    className={`mt-1 block w-full ${errors[`certifications.${index}.agency`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('certifications', index, 'agency', e.target.value)}
                                />
                                {errors[`certifications.${index}.agency`] && (
                                    <InputError message={errors[`certifications.${index}.agency`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Year Certified" />
                                <TextInput
                                    type="number"
                                    name={`certifications.${index}.dateCertified`}
                                    value={cert.dateCertified || ''}
                                    min="1900"
                                    max={new Date().getFullYear()}
                                    placeholder="YYYY"
                                    className={`mt-1 block w-full ${errors[`certifications.${index}.dateCertified`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('certifications', index, 'dateCertified', parseInt(e.target.value) || '')}
                                />
                                {errors[`certifications.${index}.dateCertified`] && (
                                    <InputError message={errors[`certifications.${index}.dateCertified`][0]} className="mt-2" />
                                )}
                            </div>
                            <div>
                                <InputLabel value="Rating" />
                                <TextInput
                                    name={`certifications.${index}.rating`}
                                    value={cert.rating}
                                    className={`mt-1 block w-full ${errors[`certifications.${index}.rating`] ? 'border-red-500' : ''}`}
                                    onChange={(e) => handleArrayFieldChange('certifications', index, 'rating', e.target.value)}
                                />
                                {errors[`certifications.${index}.rating`] && (
                                    <InputError message={errors[`certifications.${index}.rating`][0]} className="mt-2" />
                                )}
                            </div>
                        </div>
                        <div>
                            <InputLabel value="Upload Certificate" />
                            <input
                                type="file"
                                name={`certificationFile-${index}`}
                                onChange={(e) => {
                                    handleArrayFieldChange('certifications', index, 'file', e.target.files[0]);
                                }}
                                accept=".pdf,.jpg,.jpeg,.png"
                                className="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100"
                            />
                            <p className="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (max 2MB)</p>
                            {errors[`certifications.${index}.file`] && (
                                <InputError message={errors[`certifications.${index}.file`][0]} className="mt-2" />
                            )}
                        </div>
                        {formData.certifications.length > 1 && (
                            <button
                                type="button"
                                onClick={() => removeArrayItem('certifications', index)}
                                className="text-red-600 hover:text-red-800"
                            >
                                Remove Entry
                            </button>
                        )}
                    </div>
                ))}
                <PrimaryButton
                    type="button"
                    onClick={() => addArrayItem('certifications', {
                        title: '',
                        agency: '',
                        dateCertified: '',
                        rating: '',
                        file: null
                    })}
                >
                    Add Another Certification
                </PrimaryButton>
            </div>
        </div>
    );
}
