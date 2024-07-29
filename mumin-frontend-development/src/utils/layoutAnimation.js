import anime from 'animejs/lib/anime.es.js';


const layoutAnimation = (location,containerRef) => {
    const fadeInAnimation = anime.timeline({
        autoplay: false,
    });
    if(location.pathname === '/dashboard') {
        fadeInAnimation
        .add({
            targets: containerRef.current,
            translateX: ['-100%', '0%'],
            easing: 'easeOutQuart',
            duration: 1200,
        })
    }else{
        fadeInAnimation
        .add({
            targets: containerRef.current,
            opacity: [0, 1],
            easing: 'easeInOutQuad',
            duration: 600,
        })
    }

    fadeInAnimation.play();
}

export default layoutAnimation