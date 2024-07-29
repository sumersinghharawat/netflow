import React, { useEffect, useRef } from "react";
import LoginForm from "../components/Auth/Login";
import { useLocation, useParams } from "react-router";
import anime from 'animejs/lib/anime.es.js';
// import '../custom.css'

const AuthLayout = () => {
    const containerRef = useRef(null);
    const location = useLocation();
    const params = useParams()
    useEffect(() => {
        const slideInAnimation = anime.timeline({
          autoplay: false,
        });
    
        slideInAnimation
          .add({
            targets: containerRef.current,
            translateX: ['-100%', '0%'],
            easing: 'easeOutQuart',
            duration: 1200,
          })
    
        slideInAnimation.play();
    },[location.pathname])
    return (
        <div ref={containerRef} style={{ position: 'relative', width: '100%', height: '100%' }}>
            <section className="loginSection">
                <div className="container centerDiv">
                    <div className="loginBgImg"></div>
                    <div className="loginBg">
                        <div className="row">
                            <LoginForm params={params}/>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default AuthLayout;

