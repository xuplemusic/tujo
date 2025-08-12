import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth, stats }) {
    return (
        <AuthenticatedLayout
            auth={auth}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {/* Total Citizens Card */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 bg-white border-b border-gray-200">
                                <h3 className="text-lg font-semibold text-gray-600">Total Citizens</h3>
                                <p className="text-4xl font-bold text-gray-800 mt-2">{stats.total_citizens}</p>
                            </div>
                        </div>

                        {/* Total Users Card */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 bg-white border-b border-gray-200">
                                <h3 className="text-lg font-semibold text-gray-600">Total Users</h3>
                                <p className="text-4xl font-bold text-gray-800 mt-2">{stats.total_users}</p>
                            </div>
                        </div>

                        {/* Total Admins Card */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 bg-white border-b border-gray-200">
                                <h3 className="text-lg font-semibold text-gray-600">Administrators</h3>
                                <p className="text-4xl font-bold text-gray-800 mt-2">{stats.total_admins}</p>
                            </div>
                        </div>

                        {/* Total Registrars Card */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 bg-white border-b border-gray-200">
                                <h3 className="text-lg font-semibold text-gray-600">Registrars</h3>
                                <p className="text-4xl font-bold text-gray-800 mt-2">{stats.total_registrars}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
