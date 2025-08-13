import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import FaceCapture from '@/Components/FaceCapture';

export default function Show({ auth, citizen }) {
    return (
        <AuthenticatedLayout
            auth={auth}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Citizen Details</h2>}
        >
            <Head title={`Citizen - ${citizen.name}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Citizen Details Card */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h3 className="text-xl font-bold mb-4">Personal Information</h3>
                            <div className="mb-2">
                                <strong>Name:</strong> {citizen.name}
                            </div>
                            <div className="mb-2">
                                <strong>Date of Birth:</strong> {citizen.date_of_birth}
                            </div>
                            <div className="mb-2">
                                <strong>LGA:</strong> {citizen.lga}
                            </div>
                            {citizen.face_image_path && (
                                <div className="mt-4">
                                    <strong>Face Image:</strong>
                                    <img src={`/storage/${citizen.face_image_path}`} alt="Face" className="mt-2 rounded-lg shadow-md w-64 h-64 object-cover" />
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Verification Components Card */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h3 className="text-xl font-bold mb-4">Face Verification</h3>

                            <div className="mb-6">
                                <p className="text-sm text-gray-600 mb-4">
                                    Use the component below to capture and verify the citizen's face using the Clarifai API.
                                </p>
                                <FaceCapture citizen={citizen} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
