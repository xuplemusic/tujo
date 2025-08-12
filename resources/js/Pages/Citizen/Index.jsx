import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';

export default function Index({ auth, citizens, filters }) {
    const { data, setData, get } = useForm({
        search: filters.search || '',
    });

    function submit(e) {
        e.preventDefault();
        get(route('citizens.index'), {
            preserveState: true,
        });
    }

    return (
        <AuthenticatedLayout
            auth={auth}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Citizens List</h2>}
        >
            <Head title="Citizens" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex items-center justify-between mb-6">
                                <h1 className="text-2xl font-bold">Citizens</h1>
                                <div className="space-x-2">
                                    {auth.user.role === 'admin' && (
                                        <a href={route('citizens.export.csv')} className="px-6 py-2 text-white bg-gray-500 rounded-md focus:outline-none">
                                            Export CSV
                                        </a>
                                    )}
                                    {(auth.user.role === 'admin' || auth.user.role === 'registrar') && (
                                        <Link
                                            className="px-6 py-2 text-white bg-green-500 rounded-md focus:outline-none"
                                            href={route('citizens.create')}
                                        >
                                            Create Citizen
                                        </Link>
                                    )}
                                </div>
                            </div>

                            <form onSubmit={submit} className="mb-6">
                                <div className="flex items-center">
                                    <input
                                        type="text"
                                        name="search"
                                        value={data.search}
                                        onChange={(e) => setData('search', e.target.value)}
                                        className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="Search by name or LGA..."
                                    />
                                    <button
                                        type="submit"
                                        className="ml-3 px-6 py-2 text-white bg-indigo-500 rounded-md focus:outline-none"
                                    >
                                        Search
                                    </button>
                                </div>
                            </form>

                            <table className="table-fixed w-full">
                                <thead>
                                    <tr className="bg-gray-100">
                                        <th className="px-4 py-2 w-20">No.</th>
                                        <th className="px-4 py-2">Name</th>
                                        <th className="px-4 py-2">Date of Birth</th>
                                        <th className="px-4 py-2">LGA</th>
                                        <th className="px-4 py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {citizens.data.map(({ id, name, date_of_birth, lga }) => (
                                        <tr key={id}>
                                            <td className="border px-4 py-2">{id}</td>
                                            <td className="border px-4 py-2">{name}</td>
                                            <td className="border px-4 py-2">{date_of_birth}</td>
                                            <td className="border px-4 py-2">{lga}</td>
                                            <td className="border px-4 py-2">
                                                <Link
                                                    tabIndex="1"
                                                    className="px-4 py-2 text-sm text-white bg-blue-500 rounded"
                                                    href={route('citizens.show', id)}
                                                >
                                                    View
                                                </Link>
                                                {(auth.user.role === 'admin' || auth.user.role === 'registrar') && (
                                                    <Link
                                                        tabIndex="1"
                                                        className="mx-1 px-4 py-2 text-sm text-white bg-yellow-500 rounded"
                                                        href={route('citizens.edit', id)}
                                                    >
                                                        Edit
                                                    </Link>
                                                )}
                                                {auth.user.role === 'admin' && (
                                                    <Link
                                                        tabIndex="1"
                                                        className="px-4 py-2 text-sm text-white bg-red-500 rounded"
                                                        href={route('citizens.destroy', id)}
                                                        method="delete"
                                                        as="button"
                                                    >
                                                        Delete
                                                    </Link>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            <div className="mt-6">
                                <Pagination links={citizens.links} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
