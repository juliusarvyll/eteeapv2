import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import PersonalInfoStep from './Components/PersonalInfoStep';
import LearningObjectivesStep from './Components/LearningObjectivesStep';
import EducationStep from './Components/EducationStep';
import WorkExperienceStep from './Components/WorkExperienceStep';
import HonorsAwardsStep from './Components/HonorsAwardsStep';
import CreativeWorksStep from './Components/CreativeWorksStep';
import LifelongLearningStep from './Components/LifelongLearningStep';
import EssayStep from './Components/EssayStep';
import ConfirmationStep from './Components/ConfirmationStep';
import axios from 'axios';
import NavBar from '@/Components/NavBar';

const STEPS = [
    { number: 1, title: 'Personal Info' },
    { number: 2, title: 'Learning Objectives' },
    { number: 3, title: 'Education' },
    { number: 4, title: 'Work Experience' },
    { number: 5, title: 'Honors & Awards' },
    { number: 6, title: 'Creative Works' },
    { number: 7, title: 'Lifelong Learning' },
    { number: 8, title: 'Essay & Submit' }
];

export default function MultiStepForm() {
    const [currentStep, setCurrentStep] = useState(1);
    const [formData, setFormData] = useState({
        // Step 1 - Personal Info
        firstName: '',
        lastName: '',
        email: '',
        middleName: '',
        suffix: '',
        birthDate: '',
        placeOfBirth: '',
        civilStatus: '',
        sex: '',
        languages: '',
        document: null,
        status: '',
        address: '',
        zipCode: '',
        phoneNumber: '',
        nationality: '',


        // Step 2 - Learning Objectives
        firstPriority: '',
        secondPriority: '',
        thirdPriority: '',
        goalStatement: '',
        timeCommitment: '',
        overseasPlan: '',
        costPayment: '',
        otherCostPayment: '',
        completionTimeline: '',

        // Step 3 - Education
        elementarySchool: '',
        elementaryAddress: '',
        elementaryDateFrom: '',
        elementaryDateTo: '',
        hasElementaryDiploma: false,
        elementaryDiplomaFile: null,

        secondaryEducationType: 'regular',
        hasPEPT: false,
        peptYear: '',
        peptGrade: '',

        highSchools: [{
            name: '',
            address: '',
            type: '',
            dateFrom: '',
            dateTo: ''
        }],

        hasPostSecondaryDiploma: false,
        postSecondaryDiplomaFile: null,
        postSecondary: [{
            program: '',
            institution: '',
            schoolYear: ''
        }],

        nonFormalEducation: [{
            title: '',
            organization: '',
            date: '',
            certificate: '',
            participation: ''
        }],

        certifications: [{
            title: '',
            agency: '',
            dateCertified: '',
            rating: ''
        }],
        workExperiences: [],
        academicAwards: [{
            title: '',
            institution: '',
            dateReceived: '',
            description: '',
            document: null
        }],
        communityAwards: [{
            title: '',
            organization: '',
            dateAwarded: ''
        }],
        workAwards: [{
            title: '',
            organization: '',
            dateAwarded: ''
        }],
        creativeWorks: [{
            title: '',
            description: '',
            significance: '',
            dateCompleted: '',
            corroboratingBody: ''
        }],
        hobbies: [{ description: '' }],
        specialSkills: [{ description: '' }],
        workActivities: [{ description: '' }],
        volunteerActivities: [{ description: '' }],
        travels: [{ description: '' }],
        essay: '',
        birthCertificate: null,
        marriageCertificate: null,
        legalDocument: null,
        applicant_id: '',
        employment_type: 'no_employment',
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [savedStatus, setSavedStatus] = useState({
        step1: false,
        step2: false,
        step3: false,
        step4: false,
        // ... other steps
    });

    const handleInputChange = (e) => {
        const { name, value, type, checked } = e.target;

        setFormData((prev) => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value,
        }));

        // Clear error when user starts typing
        if (errors[name]) {
            setErrors((prev) => ({
                ...prev,
                [name]: '',
            }));
        }

        // Clear PEPT fields when hasPEPT is false
        if (name === 'hasPEPT' && !checked) {
            setFormData(prev => ({
                ...prev,
                peptYear: '',
                peptGrade: ''
            }));
        }
    };

    const handleArrayFieldChange = (arrayName, index, field, value) => {
        setFormData(prev => ({
            ...prev,
            [arrayName]: prev[arrayName].map((item, i) =>
                i === index ? { ...item, [field]: value } : item
            )
        }));
    };

    const addArrayItem = (arrayName, emptyItem) => {
        setFormData(prev => ({
            ...prev,
            [arrayName]: [...prev[arrayName], emptyItem]
        }));
    };

    const removeArrayItem = (arrayName, index) => {
        setFormData(prev => ({
            ...prev,
            [arrayName]: prev[arrayName].filter((_, i) => i !== index)
        }));
    };

    const handleNext = async () => {
        const stepData = getStepData(currentStep);

        setLoading(true);
        try {
            const config = {
                headers: {
                    'Content-Type': (currentStep === 1 || currentStep === 3 || currentStep === 4 || currentStep === 5 || currentStep === 6 || currentStep === 7 || currentStep === 8)
                        ? 'multipart/form-data'
                        : 'application/json',
                },
            };

            const response = await axios.post(
                `/application/step/${currentStep}`,
                stepData,
                config
            );

            if (currentStep === 1 && response.data.data?.applicant_id) {
                setFormData(prev => ({
                    ...prev,
                    applicant_id: response.data.data.applicant_id
                }));
            }

            setSavedStatus(prev => ({
                ...prev,
                [`step${currentStep}`]: true
            }));

            setCurrentStep(prev => prev + 1);
            setErrors({});
        } catch (error) {
            console.error('Raw validation errors:', error.response?.data?.errors);
            
            if (error.response?.data?.errors) {
                const transformedErrors = {};
                Object.entries(error.response.data.errors).forEach(([key, value]) => {
                    // For creative works, keep the original key format
                    transformedErrors[key] = Array.isArray(value) ? value[0] : value;
                });
                
                setErrors(transformedErrors);
                console.log('Setting errors:', transformedErrors);
            }
        } finally {
            setLoading(false);
        }
    };

    // Helper function to get relevant data for each step
    const getStepData = (step) => {
        switch (step) {
            case 1: // Personal Info
                // Create FormData object for file upload
                const formDataObj = new FormData();
                formDataObj.append('firstName', formData.firstName);
                formDataObj.append('middleName', formData.middleName);
                formDataObj.append('lastName', formData.lastName);
                formDataObj.append('suffix', formData.suffix);
                formDataObj.append('birthDate', formData.birthDate);
                formDataObj.append('placeOfBirth', formData.placeOfBirth);
                formDataObj.append('civilStatus', formData.civilStatus);
                formDataObj.append('sex', formData.sex);
                formDataObj.append('religion', formData.religion);
                formDataObj.append('languages', formData.languages);
                // Add missing required fields
                formDataObj.append('address', formData.address);
                formDataObj.append('zipCode', formData.zipCode);
                formDataObj.append('phoneNumber', formData.phoneNumber);
                formDataObj.append('email', formData.email);
                formDataObj.append('nationality', formData.nationality);

                // Only append document if it exists and is a file
                if (formData.document instanceof File) {
                    formDataObj.append('document', formData.document);
                }

                if (formData.applicant_id) {
                    formDataObj.append('applicant_id', formData.applicant_id);
                }

                return formDataObj;

            case 2: // Learning Objectives
                return {
                    applicant_id: formData.applicant_id,
                    firstPriority: formData.firstPriority,
                    secondPriority: formData.secondPriority,
                    thirdPriority: formData.thirdPriority,
                    goalStatement: formData.goalStatement,
                    timeCommitment: formData.timeCommitment,
                    overseasPlan: formData.overseasPlan,
                    costPayment: formData.costPayment,
                    otherCostPayment: formData.otherCostPayment,
                    completionTimeline: formData.completionTimeline
                };

            case 3: // Education
                const educationFormData = new FormData();

                // Convert boolean values to '1' or '0' strings for PHP
                educationFormData.append('hasElementaryDiploma', formData.hasElementaryDiploma ? '1' : '0');
                educationFormData.append('hasPEPT', formData.hasPEPT ? '1' : '0');
                educationFormData.append('hasPostSecondaryDiploma', formData.hasPostSecondaryDiploma ? '1' : '0');

                // Append basic fields
                educationFormData.append('applicant_id', formData.applicant_id);
                
                // Elementary Education - Parse years as integers
                educationFormData.append('elementarySchool', formData.elementarySchool);
                educationFormData.append('elementaryAddress', formData.elementaryAddress);
                educationFormData.append('elementaryDateFrom', parseInt(formData.elementaryDateFrom) || '');
                educationFormData.append('elementaryDateTo', parseInt(formData.elementaryDateTo) || '');
                educationFormData.append('hasElementaryDiploma', formData.hasElementaryDiploma ? '1' : '0');
                
                if (formData.elementaryDiplomaFile instanceof File) {
                    educationFormData.append('elementaryDiplomaFile', formData.elementaryDiplomaFile);
                }

                // Secondary Education
                educationFormData.append('hasPEPT', formData.hasPEPT ? '1' : '0');
                
                if (formData.hasPEPT) {
                    educationFormData.append('peptYear', parseInt(formData.peptYear) || '');
                    educationFormData.append('peptGrade', formData.peptGrade);
                } else {
                    // High Schools array
                    formData.highSchools.forEach((school, index) => {
                        educationFormData.append(`highSchools[${index}][name]`, school.name);
                        educationFormData.append(`highSchools[${index}][address]`, school.address);
                        educationFormData.append(`highSchools[${index}][type]`, school.type);
                        educationFormData.append(`highSchools[${index}][dateFrom]`, parseInt(school.dateFrom) || '');
                        educationFormData.append(`highSchools[${index}][dateTo]`, parseInt(school.dateTo) || '');
                        
                        if (school.type === 'Senior High School') {
                            educationFormData.append(`highSchools[${index}][strand]`, school.strand);
                        }
                    });
                }

                // Post Secondary Education
                formData.postSecondary.forEach((education, index) => {
                    educationFormData.append(`postSecondary[${index}][program]`, education.program);
                    educationFormData.append(`postSecondary[${index}][institution]`, education.institution);
                    educationFormData.append(`postSecondary[${index}][schoolYear]`, education.schoolYear);
                });

                // Non-Formal Education
                formData.nonFormalEducation.forEach((education, index) => {
                    educationFormData.append(`nonFormalEducation[${index}][title]`, education.title);
                    educationFormData.append(`nonFormalEducation[${index}][organization]`, education.organization);
                    educationFormData.append(`nonFormalEducation[${index}][date]`, education.date);
                    educationFormData.append(`nonFormalEducation[${index}][certificate]`, education.certificate);
                    educationFormData.append(`nonFormalEducation[${index}][participation]`, education.participation);
                });

                // Certifications
                formData.certifications.forEach((cert, index) => {
                    educationFormData.append(`certifications[${index}][title]`, cert.title);
                    educationFormData.append(`certifications[${index}][agency]`, cert.agency);
                    educationFormData.append(`certifications[${index}][dateCertified]`, parseInt(cert.dateCertified) || '');
                    educationFormData.append(`certifications[${index}][rating]`, cert.rating);
                    
                    if (cert.file instanceof File) {
                        educationFormData.append(`certifications[${index}][file]`, cert.file);
                    }
                });

                return educationFormData;

            case 4: // Work Experience
                const workExperienceFormData = new FormData();
                workExperienceFormData.append('applicant_id', formData.applicant_id);
                workExperienceFormData.append('employment_type', formData.employment_type);

                // Properly format array data for Laravel with nested fields
                formData.workExperiences?.forEach((experience, index) => {
                    // Append common fields
                    workExperienceFormData.append(`workExperiences[${index}][designation]`, experience.designation || '');
                    workExperienceFormData.append(`workExperiences[${index}][companyName]`, experience.companyName || '');
                    workExperienceFormData.append(`workExperiences[${index}][companyAddress]`, experience.companyAddress || '');
                    workExperienceFormData.append(`workExperiences[${index}][dateFrom]`, experience.dateFrom || '');
                    workExperienceFormData.append(`workExperiences[${index}][dateTo]`, experience.dateTo || '');
                    workExperienceFormData.append(`workExperiences[${index}][reasonForLeaving]`, experience.reasonForLeaving || '');
                    workExperienceFormData.append(`workExperiences[${index}][responsibilities]`, experience.responsibilities || '');

                    // Append employment type specific fields
                    if (formData.employment_type === 'employed') {
                        workExperienceFormData.append(`workExperiences[${index}][employmentStatus]`, experience.employmentStatus || '');
                        workExperienceFormData.append(`workExperiences[${index}][supervisorName]`, experience.supervisorName || '');
                    }

                    if (formData.employment_type === 'self_employed') {
                        // Append reference contacts
                        for (let refNum = 1; refNum <= 3; refNum++) {
                            workExperienceFormData.append(`workExperiences[${index}][reference${refNum}_name]`, experience[`reference${refNum}_name`] || '');
                            workExperienceFormData.append(`workExperiences[${index}][reference${refNum}_contact]`, experience[`reference${refNum}_contact`] || '');
                        }
                    }

                    // Handle file upload
                    if (experience.documents instanceof File) {
                        workExperienceFormData.append(`workExperiences[${index}][documents]`, experience.documents);
                    }
                });

                return workExperienceFormData;

            case 5: // Honors & Awards
                const honorsFormData = new FormData();
                honorsFormData.append('applicant_id', formData.applicant_id);
                
                // Academic Awards
                formData.academicAwards.forEach((award, index) => {
                    honorsFormData.append(`academicAwards[${index}][title]`, award.title);
                    honorsFormData.append(`academicAwards[${index}][institution]`, award.institution);
                    honorsFormData.append(`academicAwards[${index}][dateReceived]`, award.dateReceived);
                    honorsFormData.append(`academicAwards[${index}][description]`, award.description);
                    if (award.document instanceof File) {
                        honorsFormData.append(`academicAwards[${index}][document]`, award.document);
                    }
                });

                // Community Awards
                formData.communityAwards.forEach((award, index) => {
                    honorsFormData.append(`communityAwards[${index}][title]`, award.title);
                    honorsFormData.append(`communityAwards[${index}][organization]`, award.organization);
                    honorsFormData.append(`communityAwards[${index}][dateAwarded]`, award.dateAwarded);
                });

                // Work Awards
                formData.workAwards.forEach((award, index) => {
                    honorsFormData.append(`workAwards[${index}][title]`, award.title);
                    honorsFormData.append(`workAwards[${index}][organization]`, award.organization);
                    honorsFormData.append(`workAwards[${index}][dateAwarded]`, award.dateAwarded);
                });

                return honorsFormData;

            case 6: // Creative Works
                const creativeWorksFormData = new FormData();
                creativeWorksFormData.append('applicant_id', formData.applicant_id);

                // Append each creative work as a separate entry
                formData.creativeWorks.forEach((work, index) => {
                    creativeWorksFormData.append(`creativeWorks[${index}][title]`, work.title);
                    creativeWorksFormData.append(`creativeWorks[${index}][description]`, work.description);
                    creativeWorksFormData.append(`creativeWorks[${index}][significance]`, work.significance);
                    creativeWorksFormData.append(`creativeWorks[${index}][dateCompleted]`, work.dateCompleted);
                    creativeWorksFormData.append(`creativeWorks[${index}][corroboratingBody]`, work.corroboratingBody);
                });

                return creativeWorksFormData;

            case 7: // Lifelong Learning
                const lifelongLearningFormData = new FormData();
                lifelongLearningFormData.append('applicant_id', formData.applicant_id);

                // Hobbies
                formData.hobbies.forEach((hobby, index) => {
                    lifelongLearningFormData.append(`hobbies[${index}][description]`, hobby.description);
                });

                // Special Skills
                formData.specialSkills.forEach((skill, index) => {
                    lifelongLearningFormData.append(`specialSkills[${index}][description]`, skill.description);
                });

                // Work Activities
                formData.workActivities.forEach((activity, index) => {
                    lifelongLearningFormData.append(`workActivities[${index}][description]`, activity.description);
                });

                // Volunteer Activities
                formData.volunteerActivities.forEach((activity, index) => {
                    lifelongLearningFormData.append(`volunteerActivities[${index}][description]`, activity.description);
                });

                // Travels
                formData.travels.forEach((travel, index) => {
                    lifelongLearningFormData.append(`travels[${index}][description]`, travel.description);
                });

                return lifelongLearningFormData;

            case 8: // Essay
                const essayFormData = new FormData();
                essayFormData.append('applicant_id', formData.applicant_id);
                essayFormData.append('essay', formData.essay);
                return essayFormData;

            default:
                return {};
        }
    };

    const handlePrevious = () => {
        setCurrentStep((prev) => prev - 1);
    };

    const handleSubmit = async (e) => {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }

        setLoading(true);
        try {
            // Save current step if we're on the final step
            if (currentStep === STEPS.length) {
                await handleNext(); // This will save the essay step
            }

            // Only finalize after successful step save
            if (currentStep === STEPS.length) {
                const response = await axios.post('/application/finalize', {
                    applicant_id: formData.applicant_id,
                    status: 'pending'
                });

                setFormData(prev => ({ ...prev, status: 'pending' }));
                setCurrentStep(STEPS.length + 1);
            }
        } catch (error) {
            setErrors({ submit: 'Failed to submit application. Please try again.' });
        } finally {
            setLoading(false);
        }
    };

    // Add auto-save functionality
    useEffect(() => {
        const autoSave = async () => {
            if (formData.applicant_id && !loading) {
                try {
                    await axios.post(`/api/application/step/${currentStep}`, formData);
                    setSavedStatus(prev => ({
                        ...prev,
                        [`step${currentStep}`]: true
                    }));
                } catch (error) {
                    console.error('Auto-save failed:', error);
                }
            }
        };

        const timeoutId = setTimeout(autoSave, 30000); // Auto-save every 30 seconds
        return () => clearTimeout(timeoutId);
    }, [formData, currentStep]);

    // Load saved application
    const loadSavedApplication = async (applicantId) => {
        try {
            const response = await axios.get(`/application/${applicantId}`);
            setFormData(prev => ({
                ...prev,
                ...response.data.data
            }));
        } catch (error) {
            console.error('Failed to load application:', error);
        }
    };

    // Make sure applicant_id is set when the component loads
    useEffect(() => {
        // You might get this from props or an API call
        setFormData(prevState => ({
            ...prevState,

        }));
    }, []);

    const renderStep = () => {
        if (currentStep > STEPS.length) {
            return <ConfirmationStep formData={formData} />;
        }

        switch (currentStep) {
            case 1:
                return <PersonalInfoStep
                    formData={formData}
                    errors={errors}
                    handleInputChange={handleInputChange}
                />;
            case 2:
                return <LearningObjectivesStep
                    formData={formData}
                    errors={errors}
                    handleInputChange={handleInputChange}
                />;
            case 3:
                return <EducationStep
                    formData={formData}
                    errors={errors}
                    handleInputChange={handleInputChange}
                    handleArrayFieldChange={handleArrayFieldChange}
                    addArrayItem={addArrayItem}
                    removeArrayItem={removeArrayItem}
                />;
            case 4:
                return <WorkExperienceStep
                    formData={formData}
                    errors={errors}
                    handleInputChange={handleInputChange}
                    handleArrayFieldChange={handleArrayFieldChange}
                    addArrayItem={addArrayItem}
                    removeArrayItem={removeArrayItem}
                    setFormData={setFormData}
                />;
            case 5:
                return <HonorsAwardsStep
                    formData={formData}
                    errors={errors}
                    handleArrayFieldChange={handleArrayFieldChange}
                    addArrayItem={addArrayItem}
                    removeArrayItem={removeArrayItem}
                />;
            case 6:
                return <CreativeWorksStep
                    formData={formData}
                    errors={errors}
                    handleArrayFieldChange={handleArrayFieldChange}
                    addArrayItem={addArrayItem}
                    removeArrayItem={removeArrayItem}
                />;
            case 7:
                return <LifelongLearningStep
                    formData={formData}
                    errors={errors}
                    handleInputChange={handleInputChange}
                    handleArrayFieldChange={handleArrayFieldChange}
                    addArrayItem={addArrayItem}
                    removeArrayItem={removeArrayItem}
                />;
            case 8:
                return <EssayStep
                    formData={formData}
                    errors={errors}
                    handleInputChange={handleInputChange}
                    onSubmit={handleSubmit}
                />;
            default:
                return null;
        }
    };

    return (
        <>
            <Head title="ETEEAP Admission Form" />
            <NavBar />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {currentStep <= STEPS.length ? (
                                <>
                                    <h2 className={`mb-6 text-xl font-semibold leading-tight text-gray-800 ${currentStep === 8 ? 'hidden' : ''}`}>
                                        ETEEAP Admission Form
                                    </h2>

                                    {/* Updated Progress Bar */}
                                    <div className={`mb-8 ${currentStep === 8 ? 'hidden' : ''}`}>
                                        <div className="flex flex-wrap justify-between gap-2">
                                            {STEPS.map((step) => (
                                                <div
                                                    key={step.number}
                                                    className="flex flex-col items-center space-y-2"
                                                >
                                                    <div
                                                        className={`
                                                            flex h-8 w-8 items-center justify-center rounded-full
                                                            ${step.number <= currentStep
                                                                ? 'bg-indigo-600 text-white'
                                                                : 'bg-gray-200 text-gray-600'
                                                            }
                                                        `}
                                                    >
                                                        {step.number}
                                                    </div>
                                                    <span
                                                        className={`
                                                            text-xs text-center whitespace-nowrap
                                                            ${step.number <= currentStep
                                                                ? 'text-indigo-600 font-medium'
                                                                : 'text-gray-500'
                                                            }
                                                        `}
                                                    >
                                                        {step.title}
                                                    </span>
                                                </div>
                                            ))}
                                        </div>
                                        {/* Progress Line */}
                                        <div className="mt-4 hidden md:block">
                                            <div className="relative">
                                                <div className="absolute left-0 top-1/2 h-0.5 w-full bg-gray-200"></div>
                                                <div
                                                    className="absolute left-0 top-1/2 h-0.5 bg-indigo-600 transition-all duration-300"
                                                    style={{ width: `${((currentStep - 1) / (STEPS.length - 1)) * 100}%` }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    <form onSubmit={handleSubmit}>
                                        {renderStep()}

                                        <div className="mt-6 flex justify-between">
                                            {currentStep > 1 && (
                                                <SecondaryButton
                                                    type="button"
                                                    onClick={handlePrevious}
                                                >
                                                    Previous
                                                </SecondaryButton>
                                            )}

                                            <div className="ml-auto">
                                                {currentStep < STEPS.length ? (
                                                    <PrimaryButton
                                                        type="button"
                                                        onClick={handleNext}
                                                    >
                                                        Next
                                                    </PrimaryButton>
                                                ) : (
                                                    <div className="hidden"></div>
                                                )}
                                            </div>
                                        </div>
                                    </form>
                                </>
                            ) : (
                                renderStep()  // Shows ConfirmationStep
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
