import React, { useRef, useCallback, useState } from 'react';
import Webcam from 'react-webcam';
import { useForm } from '@inertiajs/react';

export default function FaceCapture({ citizen }) {
    const webcamRef = useRef(null);
    const [imgSrc, setImgSrc] = useState(null);
    const { setData, post, processing, errors } = useForm({
        image: null,
    });

    const capture = useCallback(() => {
        const imageSrc = webcamRef.current.getScreenshot();
        setImgSrc(imageSrc);
        // Convert base64 to a Blob
        fetch(imageSrc)
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], "face.jpg", { type: "image/jpeg" });
                setData('image', file);
            });
    }, [webcamRef, setImgSrc, setData]);

    const submit = (e) => {
        e.preventDefault();
        if (imgSrc) {
            post(route('citizens.face.store', citizen.id));
        } else {
            alert('Please capture an image first.');
        }
    };

    return (
        <div className="flex flex-col items-center">
            <div className="w-64 h-64 border-2 border-dashed rounded-lg flex items-center justify-center bg-gray-100 mb-4">
                {imgSrc ? (
                    <img src={imgSrc} alt="webcam" className="rounded-lg w-full h-full object-cover" />
                ) : (
                    <Webcam
                        audio={false}
                        ref={webcamRef}
                        screenshotFormat="image/jpeg"
                        className="rounded-lg w-full h-full object-cover"
                    />
                )}
            </div>
            <div className="space-x-4">
                <button onClick={capture} className="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
                    Capture Photo
                </button>
                <button onClick={submit} disabled={!imgSrc || processing} className="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 disabled:bg-gray-400 transition">
                    {processing ? 'Uploading...' : 'Upload Photo'}
                </button>
            </div>
            {errors.image && <div className="text-red-500 mt-2">{errors.image}</div>}
            {errors.duplicate && <div className="text-red-500 mt-2 font-bold">{errors.duplicate}</div>}
            {errors.api_error && <div className="text-red-500 mt-2">{errors.api_error}</div>}
        </div>
    );
}
