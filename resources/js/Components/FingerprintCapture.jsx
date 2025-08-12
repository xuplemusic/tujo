import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function FingerprintCapture({ citizen }) {
    const [fingerprintStatus, setFingerprintStatus] = useState('Not Scanned');
    const { data, setData, post, processing, errors } = useForm({
        fingerprint_data: '',
    });

    const handleScan = () => {
        // **TODO for user**: This is a placeholder.
        // In a real application, you would use the Web SDK provided by your
        // fingerprint scanner hardware to trigger a scan.
        // The SDK would return a fingerprint template, likely as a Base64 string.

        setFingerprintStatus('Scanning...');
        setTimeout(() => {
            const dummyFingerprintTemplate = `DUMMY_FINGERPRINT_TEMPLATE_FOR_${citizen.id}_${Date.now()}`;
            setData('fingerprint_data', dummyFingerprintTemplate);
            setFingerprintStatus('Scan Complete!');
        }, 2000); // Simulate a 2-second scan
    };

    const submit = (e) => {
        e.preventDefault();
        if (data.fingerprint_data) {
            post(route('citizens.fingerprint.store', citizen.id));
        } else {
            alert('Please scan a fingerprint first.');
        }
    };

    return (
        <div className="flex flex-col items-center p-4 border-2 border-dashed rounded-lg">
            <div className="mb-4">
                <p className="text-lg font-medium">Fingerprint Status:</p>
                <p className={`text-center font-bold ${data.fingerprint_data ? 'text-green-600' : 'text-gray-500'}`}>
                    {fingerprintStatus}
                </p>
            </div>
            {data.fingerprint_data && (
                <div className="mb-4 p-2 bg-gray-100 rounded w-full text-center">
                    <p className="text-xs text-gray-600 break-all">
                        <strong>Captured Data:</strong> {data.fingerprint_data.substring(0, 30)}...
                    </p>
                </div>
            )}
            <div className="space-x-4">
                <button onClick={handleScan} className="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
                    Scan Fingerprint
                </button>
                <button onClick={submit} disabled={!data.fingerprint_data || processing} className="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 disabled:bg-gray-400 transition">
                    {processing ? 'Saving...' : 'Save Fingerprint'}
                </button>
            </div>
            <p className="mt-4 text-sm text-gray-500 text-center">
                This is a simulation. You must integrate your fingerprint scanner's SDK here.
            </p>
            {errors.fingerprint_data && <div className="text-red-500 mt-2">{errors.fingerprint_data}</div>}
        </div>
    );
}
