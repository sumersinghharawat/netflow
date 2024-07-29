import React from 'react';

const NotFoundPage = () => {
    return (
        <div style={{ textAlign: 'center', marginTop: '100px' }}>
            <h1>404 Not Found</h1>
            <p>Oops! The page you're looking for doesn't exist.</p>
            <img
                src="/images/notFound.jpg"
                alt="404 Not Found"
                style={{ width: '50%', height: '50%' }}
            />
        </div>
    );
}

export default NotFoundPage;
