import { version } from './version.js';

let hideLoading;

import(`./image-handlers.js${version}`)
    .then(module => {
        hideLoading = module.hideLoading;
    })
    .catch(error => {
        console.error("Error loading image-handlers.js:", error);
    });


let lastScrollTop = 0; 

export function scrollEvent() {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    const navbar = document.getElementById('navbar');
    if (currentScroll > lastScrollTop) {
        // Scrolling down
        navbar.style.top = '-80px';
    } else {
        // Scrolling up
        navbar.style.top = '0';
    }
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Avoid negative values
}
export function scrollToBookmark(BookmarkId) {
    window.removeEventListener('scroll', scrollEvent);
    document.getElementById(BookmarkId).scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => {
        window.addEventListener('scroll', scrollEvent);
    }, 800);
}
export function navbarToggleHandler() {
    const nav = document.getElementById('navbarNav');
    nav.classList.toggle('hidden');
}
export function setupNavbarToggle() {
    document.getElementById('navbarToggle').addEventListener('click', navbarToggleHandler);
}
export function setupGSAPAnimations() { 
    gsap.registerPlugin(ScrollTrigger);
// GSAP Animations
gsap.from(".animate-top-fade", {
    opacity: 0,
    y: -80,
    duration: 2,
    ease: "power3.out",
    stagger: 0.5
});

gsap.from(".animate-fade-in-slow", {
    scrollTrigger: {
        trigger: ".animate-fade-in-slow",
        start: "top 90%",
        toggleActions: "play none none none"
    },
    opacity: 0,
    transform: "scale(0.5)",
    duration: 2,
    ease: "power3.out",
});

gsap.from(".animate-slide-left", {
    scrollTrigger: {
        trigger: ".animate-slide-left",
        start: "top 85%",
        toggleActions: "play none none none"
    },
    x: -200,
    opacity: 0,
    duration: 1.5,
    ease: "power2.out"
});

gsap.from(".animate-slide-right", {
    scrollTrigger: {
        trigger: ".animate-slide-right",
        start: "top 85%",
        toggleActions: "play none none none"
    },
    x: 200,
    opacity: 0,
    duration: 1.5,
    ease: "power2.out"
});
}

export function handleError(error_message) {
    hideLoading();
    document.getElementById('SegmentBtns').style.display = 'none';
    document.getElementById('classificationMessage').style.display = 'none';
    previewImages = [];
    currentImageId = [];
    classificationScores = [];
    currentPreviewIndex = 0;
    updatePreview();
    toastr.error(error_message || 'An error occurred. Please try again.');
}

export function handleWarning(warning_message) {
    hideLoading();
    toastr.warning(warning_message || 'An warning occurred. Please try again.');
}


export function showCreateBtns() {
    document.getElementById('CreateBtns').style.display = 'flex';
}

export function hideCreateBtns() {
    document.getElementById('CreateBtns').style.display = 'none';
}

export function showSegmentBtns() {
    document.getElementById('SegmentBtns').style.display = 'flex';
}

export function hideSegmentBtns() {
    document.getElementById('SegmentBtns').style.display = 'none';
}

export function showAnalyzeKeyBtn() {
    document.getElementById('AnalyzeKeyBtn').style.display = 'flex';
}

export function hideAnalyzeKeyBtn() {
    document.getElementById('AnalyzeKeyBtn').style.display = 'none';
}

export function showAnalyzeCipherBtn() {
    document.getElementById('AnalyzeCipherBtn').style.display = 'flex';
}

export function hideAnalyzeCipherBtn() {
    document.getElementById('AnalyzeCipherBtn').style.display = 'none';
}

export function showLettersKeyBtn() {
    document.getElementById('LettersKeyBtn').style.display = 'flex';
}

export function hideLettersKeyBtn() {
    document.getElementById('LettersKeyBtn').style.display = 'none';
}

export function showLettersCipherBtn() {
    document.getElementById('LettersCipherBtn').style.display = 'flex';
}

export function hideLettersCipherBtn() {
    document.getElementById('LettersCipherBtn').style.display = 'none';
}

export function showEditJSONKeyBtn() {
    document.getElementById('EditJSONKeyBtn').style.display = 'flex';
}

export function hideEditJSONKeyBtn() {
    document.getElementById('EditJSONKeyBtn').style.display = 'none';
}

export function showEditJSONCipherBtn() {
    document.getElementById('EditJSONCipherBtn').style.display = 'flex';
}

export function hideEditJSONCipherBtn() {
    document.getElementById('EditJSONCipherBtn').style.display = 'none';
}

export function showDownloadJSONBtn() {
    document.getElementById('DownloadJSONBtn').style.display = 'flex';
}

export function hideDownloadJSONBtn() {
    document.getElementById('DownloadJSONBtn').style.display = 'none';
}

export function showSystemMessage() {
    document.getElementById('SystemMessage').style.display = 'block';
}

export function hideSystemMessage() {
    document.getElementById('SystemMessage').style.display = 'none';
}