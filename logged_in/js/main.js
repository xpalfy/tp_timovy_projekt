import { version } from './version.js';

Promise.all([
    import(`./image-handlers.js${version}`),
    import(`./ui-animation-handler.js${version}`)
]).then(([imageHandlers, uiAnimationHandlers]) => {
    const {
        handleDrop, uploadImageButton, handleDragOver, handleDragLeave, downloadJSON,
        hideLoading, checkToasts, setStep, setupPreviewNavigation, segmentCipher,
        deleteUnsavedImage, segmentKey, analizeKey, analizeCipher, lettersKey, lettersCipher,
        editJSONKey, editJSONCipher, saveProcessing, deleteImage, getImage, getImageKey, saveKey, saveCipher,
        currentImageId
    } = imageHandlers;

    const {
        scrollEvent, scrollToBookmark, setupNavbarToggle, setupGSAPAnimations
    } = uiAnimationHandlers;

    // Expose functions to the global scope
    window.handleDrop = handleDrop;
    window.uploadImageButton = uploadImageButton;
    window.handleDragOver = handleDragOver;
    window.handleDragLeave = handleDragLeave;
    window.segmentCipher = segmentCipher;
    window.segmentKey = segmentKey;
    window.analizeKey = analizeKey; // Expose analizeKey
    window.analizeCipher = analizeCipher; // Expose analizeCipher
    window.lettersKey = lettersKey; // Expose lettersKey
    window.lettersCipher = lettersCipher; // Expose lettersCipher
    window.editJSONKey = editJSONKey; // Expose editJSONKey
    window.editJSONCipher = editJSONCipher; // Expose editJSONCipher
    window.deleteImage = deleteImage; // Expose deleteImage
    window.getImage = getImage; // Expose getImage
    window.getImageKey = getImageKey; // Expose getImageKey
    window.deleteUnsavedImage = deleteUnsavedImage; // Expose deleteUnsavedImage
    window.saveKey = saveKey; // Expose saveKey
    window.saveCipher = saveCipher; // Expose saveCipher
    window.scrollToBookmark = scrollToBookmark;
    window.downloadJSON = downloadJSON;
    wnindow.saveProcessing = saveProcessing;


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

    document.addEventListener('DOMContentLoaded', () => {
        setupNavbarToggle();
        setupGSAPAnimations();
        setupPreviewNavigation();
        hideLoading();
        setStep(0);
        checkToasts();
    });
}).catch(error => {
    console.error("Error loading modules:", error);
});