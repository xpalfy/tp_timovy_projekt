import { version } from './version.js';

Promise.all([
    import(`./image-handlers.js${version}`),
    import(`./ui-animation-handler.js${version}`)
]).then(([imageHandlers, uiAnimationHandlers]) => {
    const {
        handleDrop, uploadImageButton, handleDragOver, handleDragLeave, downloadJSON,
        setupPreviewNavigation, goToSegmentation,
        deleteUnsavedImage, goToAnalyzation, goToLetterSegmentation,
        goToJsonEdit, saveProcessing, deleteImage, getImage, getImageKey, saveKey, saveCipher,
        currentImageId
    } = imageHandlers;

    const {
        scrollEvent, scrollToBookmark, setupNavbarToggle, setupGSAPAnimations, setStep,
        checkToasts, hideLoading
    } = uiAnimationHandlers;

    // Expose functions to the global scope
    window.handleDrop = handleDrop;
    window.uploadImageButton = uploadImageButton;
    window.handleDragOver = handleDragOver;
    window.handleDragLeave = handleDragLeave;
    window.goToSegmentation = goToSegmentation; // Expose goToSegmentation
    window.goToAnalyzation = goToAnalyzation; // Expose goToAnalyzation
    window.goToLetterSegmentation = goToLetterSegmentation; // Expose goToLetterSegmentation
    window.goToJsonEdit = goToJsonEdit;
    window.deleteImage = deleteImage; // Expose deleteImage
    window.getImage = getImage; // Expose getImage
    window.getImageKey = getImageKey; // Expose getImageKey
    window.deleteUnsavedImage = deleteUnsavedImage; // Expose deleteUnsavedImage
    window.saveKey = saveKey; // Expose saveKey
    window.saveCipher = saveCipher; // Expose saveCipher
    window.scrollToBookmark = scrollToBookmark;
    window.downloadJSON = downloadJSON;
    window.saveProcessing = saveProcessing;


    // Setup event listeners
    window.addEventListener('drop', handleDrop);
    window.addEventListener('dragover', handleDragOver);
    window.addEventListener('dragleave', handleDragLeave);
    window.addEventListener('scroll', scrollEvent);
    window.addEventListener("beforeunload", function () {
        console.log("Page is being unloaded...");
        // Perform any necessary cleanup or state saving here
        deleteUnsavedImage(currentImageId);

        // Optionally call deleteUnsavedImage if needed
    });

    if (document.readyState === "complete" || document.readyState === "interactive") {
        initializePage();
    } else {
        document.addEventListener('DOMContentLoaded', initializePage);
    }

    function initializePage() {
        setupNavbarToggle();
        setupGSAPAnimations();
        setupPreviewNavigation();
        hideLoading();
        setStep(0);
        checkToasts();
    }
}).catch(error => {
    console.error("Error loading modules:", error);
});