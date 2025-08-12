import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Create({ auth }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        role: 'registrar',
    });

    function submit(e) {
        e.preventDefault();
        post(route('users.store'));
    }

    return (
        <AuthenticatedLayout
            auth={auth}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Create User</h2>}
        >
            <Head title="Create User" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <form onSubmit={submit}>
                                <div className="mb-4">
                                    <label className="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                    <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" />
                                    {errors.name && <div className="text-red-500 mt-2">{errors.name}</div>}
                                </div>
                                <div className="mb-4">
                                    <label className="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                    <input type="email" value={data.email} onChange={e => setData('email', e.target.value)} className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" />
                                    {errors.email && <div className="text-red-500 mt-2">{errors.email}</div>}
                                </div>
                                <div className="mb-4">
                                    <label className="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                    <input type="password" value={data.password} onChange={e => setData('password', e.target.value)} className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" />
                                    {errors.password && <div className="text-red-500 mt-2">{errors.password}</div>}
                                </div>
                                <div className="mb-4">
                                    <label className="block text-gray-700 text-sm font-bold mb-2">Role</label>
                                    <select value={data.role} onChange={e => setData('role', e.target.value)} className="shadow border rounded w-full py-2 px-3 text-gray-700">
                                        <option value="registrar">Registrar</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    {errors.role && <div className="text-red-500 mt-2">{errors.role}</div>}
                                </div>
                                <button type="submit" className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" disabled={processing}>
                                    Create
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
