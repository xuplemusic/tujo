import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Profile({ auth, tokens, success, token }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
    });

    const { delete: deleteToken } = useForm();

    function submit(e) {
        e.preventDefault();
        post(route('api-tokens.store'), {
            preserveScroll: true,
        });
    }

    function removeToken(tokenId) {
        if (confirm('Are you sure you want to delete this token?')) {
            deleteToken(route('api-tokens.destroy', tokenId), {
                preserveScroll: true,
            });
        }
    }

    return (
        <AuthenticatedLayout
            auth={auth}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">My Profile</h2>}
        >
            <Head title="Profile" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {/* Create Token Form */}
                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <section>
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">Create API Token</h2>
                                <p className="mt-1 text-sm text-gray-600">
                                    API tokens allow third-party services to authenticate with our application on your behalf.
                                </p>
                            </header>

                            {token && (
                                <div className="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                    <p className="font-bold">New token created! Please copy it now. You won't be able to see it again.</p>
                                    <code className="block bg-gray-800 text-white p-2 rounded mt-2 break-all">{token}</code>
                                </div>
                            )}

                            <form onSubmit={submit} className="mt-6 space-y-6">
                                <div>
                                    <label htmlFor="name" className="block font-medium text-sm text-gray-700">Token Name</label>
                                    <input id="name" type="text" className="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} required autoFocus />
                                    {errors.name && <p className="mt-2 text-sm text-red-600">{errors.name}</p>}
                                </div>
                                <div className="flex items-center gap-4">
                                    <button type="submit" className="px-4 py-2 bg-indigo-600 text-white rounded-md" disabled={processing}>Create</button>
                                </div>
                            </form>
                        </section>
                    </div>

                    {/* Manage Tokens List */}
                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <section>
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">Manage API Tokens</h2>
                                <p className="mt-1 text-sm text-gray-600">
                                    You may delete any of your existing tokens if they are no longer needed.
                                </p>
                            </header>

                            <div className="mt-6 space-y-4">
                                {tokens.map((t) => (
                                    <div key={t.id} className="flex items-center justify-between">
                                        <div>
                                            <div className="font-medium text-gray-900">{t.name}</div>
                                            <div className="text-sm text-gray-500">
                                                Last used: {t.last_used_at ? new Date(t.last_used_at).toLocaleString() : 'Never'}
                                            </div>
                                        </div>
                                        <button onClick={() => removeToken(t.id)} className="text-red-600 hover:text-red-900">Delete</button>
                                    </div>
                                ))}
                                {tokens.length === 0 && <p className="text-gray-500">You have not created any API tokens.</p>}
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
