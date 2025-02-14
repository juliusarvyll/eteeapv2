import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';

export default function PersonalInfoStep({ formData, errors, handleInputChange }) {
    // Add console.log to debug the form data being sent
    console.log('Form Data:', formData);

    // Helper function to handle file changes
    const handleFileChange = (e) => {
        const file = e.target.files[0];
        handleInputChange({
            target: {
                name: e.target.name,
                value: file,
                type: 'file'
            }
        });
    };

    // Function to render required document based on civil status
    const renderRequiredDocument = () => {
        const documentConfig = {
            'Single': {
                label: 'Birth Certificate (PSA)',
                description: 'Upload PSA Birth Certificate (PDF, JPG, PNG format)'
            },
            'Married': {
                label: 'Marriage Certificate (PSA)',
                description: 'Upload PSA Marriage Certificate (PDF, JPG, PNG format)'
            },
            'Separated': {
                label: 'Legal Separation Document',
                description: 'Upload Legal Separation Document (PDF, JPG, PNG format)'
            },
            'Widow': {
                label: 'Death Certificate of Spouse (PSA)',
                description: 'Upload Death Certificate of Spouse (PDF, JPG, PNG format)'
            },
            'Divorced': {
                label: 'Divorce Papers',
                description: 'Upload Divorce Papers (PDF, JPG, PNG format)'
            }
        };

        const config = documentConfig[formData.civilStatus];
        if (!config) return null;

        return (
            <div className="space-y-2">
                <InputLabel htmlFor="document" value={config.label} />
                <input
                    type="file"
                    id="document"
                    name="document"
                    onChange={handleFileChange}
                    accept=".pdf,.jpg,.jpeg,.png"
                    className={`block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100
                        ${errors.document ? 'border-red-500' : ''}`}
                />
                <p className="text-xs text-gray-500">{config.description}</p>
                {errors.document && (
                    <InputError message={errors.document} className="mt-1" />
                )}
            </div>
        );
    };

    return (
        <div className="space-y-6">
            {/* Name Section - Added Suffix */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div className="col-span-1">
                    <InputLabel htmlFor="lastName" value="Last Name" required />
                    <TextInput
                        id="lastName"
                        name="lastName"
                        value={formData.lastName}
                        className={`mt-1 block w-full ${errors.lastName ? 'error-field border-red-500' : ''}`}
                        onChange={handleInputChange}
                    />
                    {errors.lastName && <InputError message={errors.lastName} className="mt-2" />}
                </div>
                <div className="col-span-1">
                    <InputLabel htmlFor="firstName" value="First Name" required />
                    <TextInput
                        id="firstName"
                        name="firstName"
                        value={formData.firstName}
                        className={`mt-1 block w-full ${errors.firstName ? 'error-field border-red-500' : ''}`}
                        onChange={handleInputChange}
                    />
                    {errors.firstName && <InputError message={errors.firstName} className="mt-2" />}
                </div>
                <div className="col-span-1">
                    <InputLabel htmlFor="middleName" value="Middle Name" />
                    <TextInput
                        id="middleName"
                        name="middleName"
                        value={formData.middleName}
                        className="mt-1 block w-full"
                        onChange={handleInputChange}
                    />
                </div>
                <div className="col-span-1">
                    <InputLabel htmlFor="suffix" value="Suffix" />
                    <TextInput
                        id="suffix"
                        name="suffix"
                        value={formData.suffix}
                        className="mt-1 block w-full"
                        onChange={handleInputChange}
                        placeholder="e.g., Jr., Sr., III"
                    />
                </div>
            </div>

            {/* Added Religion Field */}
            <div>
                <InputLabel htmlFor="religion" value="Religion" />
                <TextInput
                    id="religion"
                    name="religion"
                    value={formData.religion}
                    className="mt-1 block w-full"
                    onChange={handleInputChange}
                />
            </div>

            {/* Address */}
            <div>
                <InputLabel htmlFor="address" value="Complete Address" required />
                <TextInput
                    id="address"
                    name="address"
                    value={formData.address}
                    className={`mt-1 block w-full ${errors.address ? 'error-field border-red-500' : ''}`}
                    placeholder="Building number/name, street name, district, city, province"
                    onChange={handleInputChange}
                />
                {errors.address && (
                    <InputError message={errors.address} className="mt-2" />
                )}
            </div>

            {/* ZIP Code */}
            <div>
                <InputLabel htmlFor="zipCode" value="ZIP Code" required />
                <TextInput
                    id="zipCode"
                    name="zipCode"
                    value={formData.zipCode}
                    className={`mt-1 block w-full ${errors.zipCode ? 'error-field border-red-500' : ''}`}
                    onChange={handleInputChange}
                />
                {errors.zipCode && (
                    <InputError message={errors.zipCode} className="mt-2" />
                )}
            </div>

            {/* Contact Number - Fix field name */}
            <div>
                <InputLabel htmlFor="phoneNumber" value="Telephone/Mobile Number(s)" required />
                <TextInput
                    id="phoneNumber"
                    name="phoneNumber"
                    value={formData.phoneNumber}
                    className={`mt-1 block w-full ${errors.phoneNumber ? 'error-field border-red-500' : ''}`}
                    placeholder="Include area code for areas outside Metro Manila"
                    onChange={handleInputChange}
                />
                {errors.phoneNumber && (
                    <InputError message={errors.phoneNumber} className="mt-2" />
                )}
            </div>

            {/* Add Missing Email Field */}
            <div>
                <InputLabel htmlFor="email" value="Email Address" required />
                <TextInput
                    id="email"
                    name="email"
                    type="email"
                    value={formData.email}
                    className={`mt-1 block w-full ${errors.email ? 'error-field border-red-500' : ''}`}
                    onChange={handleInputChange}
                />
                {errors.email && (
                    <InputError message={errors.email} className="mt-2" />
                )}
            </div>

            {/* Birth Information */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <InputLabel htmlFor="birthDate" value="Birth Date" required />
                    <TextInput
                        id="birthDate"
                        name="birthDate"
                        type="date"
                        value={formData.birthDate}
                        className={`mt-1 block w-full ${errors.birthDate ? 'error-field border-red-500' : ''}`}
                        onChange={handleInputChange}
                    />
                    {errors.birthDate && (
                        <InputError message={errors.birthDate} className="mt-2" />
                    )}
                </div>
                <div>
                    <InputLabel htmlFor="placeOfBirth" value="Place of Birth" required />
                    <TextInput
                        id="placeOfBirth"
                        name="placeOfBirth"
                        value={formData.placeOfBirth}
                        className={`mt-1 block w-full ${errors.placeOfBirth ? 'error-field border-red-500' : ''}`}
                        placeholder="City/Municipality and Province"
                        onChange={handleInputChange}
                    />
                    {errors.placeOfBirth && (
                        <InputError message={errors.placeOfBirth} className="mt-2" />
                    )}
                </div>
            </div>

            {/* Civil Status with Required Document */}
            <div className="space-y-4">
                <div>
                    <InputLabel value="Civil Status" required />
                    <div className={`mt-2 space-y-2 ${errors.civilStatus ? 'error-field' : ''}`}>
                        {['Single', 'Married', 'Separated', 'Widow', 'Divorced'].map((status) => (
                            <label key={status} className="flex items-center">
                                <input
                                    type="radio"
                                    name="civilStatus"
                                    value={status}
                                    checked={formData.civilStatus === status}
                                    onChange={handleInputChange}
                                    className={`mr-2 ${errors.civilStatus ? 'border-red-500' : ''}`}
                                />
                                {status}
                            </label>
                        ))}
                    </div>
                    {errors.civilStatus && (
                        <InputError message={errors.civilStatus} className="mt-2" />
                    )}
                </div>

                {/* Required Document Section */}
                {formData.civilStatus && (
                    <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h3 className="text-sm font-semibold text-gray-700 mb-4">Required Document</h3>
                        {renderRequiredDocument()}
                    </div>
                )}
            </div>

            {/* Sex */}
            <div>
                <InputLabel value="Sex" required />
                <div className={`mt-2 space-y-2 ${errors.sex ? 'error-field' : ''}`}>
                    {['Male', 'Female'].map((sex) => (
                        <label key={sex} className="flex items-center">
                            <input
                                type="radio"
                                name="sex"
                                value={sex}
                                checked={formData.sex === sex}
                                onChange={handleInputChange}
                                className={`mr-2 ${errors.sex ? 'border-red-500' : ''}`}
                            />
                            {sex}
                        </label>
                    ))}
                </div>
                {errors.sex && (
                    <InputError message={errors.sex} className="mt-2" />
                )}
            </div>

            {/* Nationality */}
            <div>
                <InputLabel htmlFor="nationality" value="Nationality" required />
                <TextInput
                    id="nationality"
                    name="nationality"
                    value={formData.nationality}
                    className={`mt-1 block w-full ${errors.nationality ? 'error-field border-red-500' : ''}`}
                    onChange={handleInputChange}
                />
                {errors.nationality && (
                    <InputError message={errors.nationality} className="mt-2" />
                )}
            </div>

            {/* Languages */}
            <div>
                <InputLabel htmlFor="languages" value="Languages and Dialects" required />
                <TextInput
                    id="languages"
                    name="languages"
                    value={formData.languages}
                    className={`mt-1 block w-full ${errors.languages ? 'error-field border-red-500' : ''}`}
                    placeholder="e.g., English, Filipino, Ibanag, Ilokano, Cebuano, etc."
                    onChange={handleInputChange}
                />
                {errors.languages && (
                    <InputError message={errors.languages} className="mt-2" />
                )}
            </div>
        </div>
    );
}
