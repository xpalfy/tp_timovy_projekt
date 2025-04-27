let doc_id = null;
let item_id = null;
let currentImageId = [];
let previewImages = [];
let classificationScores = [];
let currentPreviewIndex = 0;
let numOfFiles = 0;
let lastScrollTop = 0;

function handleFile(file, shouldShow, first) {
    disableClickUpload();
    if (file.type.match('image.*')) {
        let reader = new FileReader();
        reader.onload = function (e) {
            if (currentImageId.length > 0 && first) {
                console.log("Deleting previous unsaved images...");
                deleteUnsavedImage(currentImageId);
            }
            image_name = file.name.split('.')[0].split(' ').join('_').toLowerCase();
            previewImages.push([e.target.result, image_name]);
            console.log("Image uploaded:", previewImages);
            if (shouldShow) {
                currentPreviewIndex = previewImages.length - 1;
                updatePreview();
            }
            saveImage(e.target.result, image_name);
        };
        reader.readAsDataURL(file);
    } else {
        toastr.error('Please upload image files only.');
    }
}

function handleDrop(event) {
    event.preventDefault();
    const files = event.dataTransfer.files;
    showLoading();
    numOfFiles = files.length;
    if (files.length > 1) {
        showScrollBtns();
    } else {
        hideScrollBtns();
    }
    for (let i = 0; i < files.length; i++) {
        handleFile(files[i], i === files.length - 1, i === 0);
    }
}

function uploadImageButton(event) {
    const files = event.target.files;
    showLoading();
    numOfFiles = files.length;
    if (files.length > 1) {
        showScrollBtns();
    } else {
        hideScrollBtns();
    }
    for (let i = 0; i < files.length; i++) {
        handleFile(files[i], i === files.length - 1, i === 0);
    }
}

function handleDragOver(event) {
    event.preventDefault();
    document.getElementById('imageUploader').style.border = '4px dashed #eab308';
}

function handleDragLeave() {
    document.getElementById('imageUploader').style.border = '#eab308 dashed 4px';
}

function updatePreview() {
    let imageElements = document.getElementsByClassName('imagePreview');
    for (imageElement of imageElements) {
        if (previewImages.length === 0) {
            imageElement.style.display = 'none';
            continue;
        }
        imageElement.src = previewImages[currentPreviewIndex][0];
        imageElement.style.display = 'block';
    }
}

function showScrollBtns() {
    buttons = document.getElementsByClassName('prevBtn');
    for (let button of buttons) {
        button.style.visibility = 'visible';
    }
    buttons = document.getElementsByClassName('nextBtn');
    for (let button of buttons) {
        button.style.visibility = 'visible';
    }
}

function hideScrollBtns() {
    buttons = document.getElementsByClassName('prevBtn');
    for (let button of buttons) {
        button.style.visibility = 'hidden';
    }
    buttons = document.getElementsByClassName('nextBtn');
    for (let button of buttons) {
        button.style.visibility = 'hidden';
    }
}

buttons = document.getElementsByClassName('prevBtn');
for (let button of buttons) {
    button.addEventListener('click', function () {
        if (previewImages.length === 0) return;
        currentPreviewIndex = (currentPreviewIndex - 1 + previewImages.length) % previewImages.length;
        updatePreview();
    });
}

buttons = document.getElementsByClassName('nextBtn');
for (let button of buttons) {
    button.addEventListener('click', function () {
        if (previewImages.length === 0) return;
        currentPreviewIndex = (currentPreviewIndex + 1) % previewImages.length;
        updatePreview();
    });
}

function saveData(type) {
    console.log(previewImages);
    if (previewImages.length === 0) {
        handleError('Please upload an image first.');
        return;
    }
    doc_name = document.getElementById('documentName').value;
    if (doc_name === '') {
        handleError('Please enter a name for the document.');
        return;
    }

    console.log(doc_name);

    fetch('documents/createDocument.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            doc_name: doc_name,
            type: type,
            user_name: window.userData.username, 
            id: window.userData.id, 
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                console.log("Document created successfully.");
                doc_id = data.document_id;
                console.log("Document ID:", doc_id);
                for (let [data, image_name] of previewImages) {
                    fetch('items/createItem.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            image_name: image_name,
                            doc_id: doc_id,
                            doc_name: doc_name,
                            type: type,
                            user_name: window.userData.username, // Use injected data
                            id: window.userData.id,              // No PHP in JS file!
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                currentImageId = null; // Image is saved, no need to delete it
                                item_id = data.item_id;
                                console.log("Item created successfully. ID:", item_id);
                            } else {
                                handleError(data.error);
                            }
                        });
                }
                toastr.success('Images uploaded successfully.');
                // reset window
                currentImageId = [];
                previewImages = [];
                classificationScores = [];
                currentPreviewIndex = 0;
                updatePreview();
                hideSegmentBtns();
                hideAnalyzeKeyBtn();
                hideAnalyzeCipherBtn();
                hideLettersKeyBtn();
                hideLettersCipherBtn();
                hideEditJSONKeyBtn();
                hideEditJSONCipherBtn();
                hideSaveKeyBtns();
                hideSaveCipherBtns();
                hideLoading();
                hideSystemMessage();
                setStep(0);
            } else {
                handleWarning(data.error);
                return;
            }
        });
}

function saveKey() {
    saveData('KEY');
}

function saveCipher() {
    saveData('CIPHER');
}

function saveImage(data, image_name) {
    fetch('savePicture.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            data: data,
            data_name: image_name,
            user_name: window.userData.username, // Use injected data
            id: window.userData.id,              // No PHP in JS file!
            type: 'temp',
        })
    }).then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);

                currentImageId.push(data.picture_id); // Store the temporary image ID
                console.log("Image uploaded successfully. ID:", currentImageId);
                classificationScores.push(classifyPicture(data.path));
                if (classificationScores.length === numOfFiles && classificationScores.length > 0) {
                    showSegmentBtns();
                    applyClassificationStyle(classificationScores);
                }
            } else {
                handleError(data.error);
            }
        });
}

function showSegmentBtns() {
    document.getElementById('SegmentBtns').style.display = 'flex';
}

function hideSegmentBtns() {
    document.getElementById('SegmentBtns').style.display = 'none';
}

function showAnalyzeKeyBtn() {
    document.getElementById('AnalyzeKeyBtn').style.display = 'flex';
}

function hideAnalyzeKeyBtn() {
    document.getElementById('AnalyzeKeyBtn').style.display = 'none';
}

function showAnalyzeCipherBtn() {
    document.getElementById('AnalyzeCipherBtn').style.display = 'flex';
}

function hideAnalyzeCipherBtn() {
    document.getElementById('AnalyzeCipherBtn').style.display = 'none';
}

function showLettersKeyBtn() {
    document.getElementById('LettersKeyBtn').style.display = 'flex';
}

function hideLettersKeyBtn() {
    document.getElementById('LettersKeyBtn').style.display = 'none';
}

function showLettersCipherBtn() {
    document.getElementById('LettersCipherBtn').style.display = 'flex';
}

function hideLettersCipherBtn() {
    document.getElementById('LettersCipherBtn').style.display = 'none';
}

function showEditJSONKeyBtn() {
    document.getElementById('EditJSONKeyBtn').style.display = 'flex';
}

function hideEditJSONKeyBtn() {
    document.getElementById('EditJSONKeyBtn').style.display = 'none';
}

function showEditJSONCipherBtn() {
    document.getElementById('EditJSONCipherBtn').style.display = 'flex';
}

function hideEditJSONCipherBtn() {
    document.getElementById('EditJSONCipherBtn').style.display = 'none';
}

function showSaveKeyBtns() {
    document.getElementById('SaveKeyBtns').style.display = 'flex';
}

function hideSaveKeyBtns() {
    document.getElementById('SaveKeyBtns').style.display = 'none';
}

function showSaveCipherBtns() {
    document.getElementById('SaveCipherBtns').style.display = 'flex';
}

function hideSaveCipherBtns() {
    document.getElementById('SaveCipherBtns').style.display = 'none';
}

function deleteUnsavedImage(imageId) {
    console.log("Deleting unsaved images...");
    for (let image_id of imageId) {
        fetch(`items/deleteItem.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_name: window.userData.username, // Use injected data
                id: window.userData.id,              // No PHP in JS file!
                image_id: image_id,
                item_id: item_id,
            })
        }).then(response => {
            if (response.ok) {
                console.log("Unsaved image deleted successfully.");
                toastr.success("Unsaved image deleted successfully.");
            } else {
                console.error("Failed to delete unsaved image.");
                toastr.error("Failed to delete unsaved image.");
            }
        }).catch(error => {
            handleError("Error deleting unsaved image: " + error);
        });
    }
    currentImageId = [];
    previewImages = [];
    classificationScores = [];
    currentPreviewIndex = 0;
    updatePreview();
    hideSegmentBtns();
    hideAnalyzeKeyBtn();
    hideAnalyzeCipherBtn();
    hideLettersKeyBtn();
    hideLettersCipherBtn();
    hideEditJSONKeyBtn();
    hideEditJSONCipherBtn();
    hideSaveKeyBtns();
    hideSaveCipherBtns();
    hideLoading();
    hideSystemMessage();
}

async function classifyPicture(path) {
    const url = 'https://python.tptimovyprojekt.software/classify';
    console.log("Sending request to Flask server...");

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ path })  // Sending JSON data
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
            handleError();
        }

        const data = await response.json();

        if (data.classification) {
            console.log("Classification:", data.classification);
            toastr.success(`Classification: ${data.classification}`);
            return data.classification;
        } else {
            console.error("Error in classification response.");
            handleError();
        }
    } catch (error) {
        console.error("Error sending request to Flask server:", error.message);
        handleError();
    }
}

function checkToasts() {
    if (window.toastData) {
        toastr[window.toastData.type](window.toastData.message);
    }
}

function applyClassificationStyle(classification_score) {
    let parentOfBtns = document.getElementById('SegmentBtns');
    let segmentKeyBtn = parentOfBtns.children[0];
    let segmentCipherBtn = parentOfBtns.children[1];
    let messageContainer = document.getElementById('SystemMessage');

    score = 0;
    Promise.all(classificationScores).then(value => {
        for (let i = 0; i < value.length; i++) {
            score += value[i];
        }
        classification_score = score / classification_score.length;

        // Reset styles
        segmentCipherBtn.style.border = "2px solidrgb(0, 0, 0)";
        segmentCipherBtn.style.padding = "9px";
        segmentKeyBtn.style.border = "2px solidrgb(0, 0, 0)";
        segmentKeyBtn.style.padding = "9px";

        if (classification_score > 50) {
            segmentCipherBtn.style.border = "2px solid green";
            segmentCipherBtn.style.padding = "9px";
            messageContainer.innerHTML = `The classifier thinks the images are ${classification_score}% ciphertexts.`;
        } else {
            segmentKeyBtn.style.border = "2px solid green";
            segmentKeyBtn.style.padding = "9px";
            messageContainer.innerHTML = `The classifier thinks the images are ${100 - classification_score}% keys.`;
        }
        hideLoading();
        showSystemMessage();
    })
}

function showLoading() {
    loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'flex';
    }
}

function hideLoading() {
    loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'none';
    }
}

function showSystemMessage() {
    document.getElementById('SystemMessage').style.display = 'block';
}

function hideSystemMessage() {
    document.getElementById('SystemMessage').style.display = 'none';
}

function disableClickUpload() {
    document.getElementById('imageUploader').removeAttribute('onclick');
    document.getElementById('uploadBtn').setAttribute('onclick', 'document.getElementById("fileInput").click()');
}

function setStep(index) {
    const steps = document.querySelectorAll('.step');
    const lines = document.querySelectorAll('.line');

    steps.forEach((step, i) => {
        if (i <= index) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });

    lines.forEach((line, i) => {
        if (i < index) {
            line.style.backgroundColor = '#bfa97a';
        } else {
            line.style.backgroundColor = '#cdbf9b';
        }
    });
}

function handleError(error_message) {
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

function handleWarning(warning_message) {
    hideLoading();
    toastr.warning(warning_message || 'An warning occurred. Please try again.');
}

function segmentCipher() {
    setStep(1);
    hideSegmentBtns();
    hideSystemMessage();
    showAnalyzeCipherBtn();
    document.getElementById('imageUploader').style.display = 'none';
    document.getElementById('imageSegmentor').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
    updatePreview();
    CalculateSegmentation('Cipher');
}

function segmentKey() {
    setStep(1);
    hideSegmentBtns();
    hideSystemMessage();
    showAnalyzeKeyBtn();
    document.getElementById('imageUploader').style.display = 'none';
    document.getElementById('imageSegmentor').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
    updatePreview();
    CalculateSegmentation('Key');
}

function analizeKey() {
    setStep(2);
    hideAnalyzeKeyBtn();
    hideSystemMessage();
    showLettersKeyBtn();
    document.getElementById('imageSegmentor').style.display = 'none';
    document.getElementById('imageAnalyzer').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to analyze the images.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
    updatePreview();
    CalculateAnalization('Key');
}

function analizeCipher() {
    setStep(2);
    hideAnalyzeCipherBtn();
    hideSystemMessage();
    showLettersCipherBtn();
    document.getElementById('imageSegmentor').style.display = 'none';
    document.getElementById('imageAnalyzer').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to analyze the images.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
    updatePreview();
    CalculateAnalization('Cipher');
}

function lettersKey() {
    setStep(3);
    hideLettersKeyBtn();
    hideSystemMessage();
    showEditJSONKeyBtn();
    document.getElementById('imageAnalyzer').style.display = 'none';
    document.getElementById('imageLetters').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to segment the letters.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
    updatePreview();
    CalculateLetters('Key');
}

function lettersCipher() {
    setStep(3);
    hideLettersCipherBtn();
    hideSystemMessage();
    showEditJSONCipherBtn();
    document.getElementById('imageAnalyzer').style.display = 'none';
    document.getElementById('imageLetters').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to segment the letters.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
    updatePreview();
    CalculateLetters('Cipher');
}

function editJSONKey() {
    setStep(4);
    hideEditJSONKeyBtn();
    hideSystemMessage();
    showSaveKeyBtns();
    document.getElementById('imageLetters').style.display = 'none';
    document.getElementById('imageJSON').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
}

function editJSONCipher() {
    setStep(4);
    hideEditJSONCipherBtn();
    hideSystemMessage();
    showSaveKeyBtns();
    document.getElementById('imageLetters').style.display = 'none';
    document.getElementById('imageJSON').style.display = 'block';
    document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
    // got to #Dashboard
    scrollToBookmark('bookmark');
}

function CalculateSegmentation(type) {
    showLoading();

    const imagePath = 'path/to/your/image.jpg';

    fetch('https://python.tptimovyprojekt.software/segmentate_page', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ path: imagePath })
    })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.polygon && Array.isArray(data.polygon)) {
                appendSegmentedRect(data.polygon);
            } else {
                console.error('Invalid response from server:', data);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error detecting page edges:', error);
        });
}

function appendSegmentedRect(Rect) {
    if (Rect.length !== 4) {
        console.error('Invalid Rect:', Rect);
        return;
    }
    let parent = document.getElementById('previewContainerSegment');
    let x2 = Rect[0];
    let y2 = Rect[3];
    let x4 = Rect[2];
    let y4 = Rect[1];

    let newRect = document.createElement('segment-rect');
    newRect.setAttribute('x1', Rect[0]);
    newRect.setAttribute('y1', Rect[1]);
    newRect.setAttribute('x2', x2);
    newRect.setAttribute('y2', y2);
    newRect.setAttribute('x3', Rect[2]);
    newRect.setAttribute('y3', Rect[3]);
    newRect.setAttribute('x4', x4);
    newRect.setAttribute('y4', y4);
    newRect.setAttribute('style', 'position: absolute; width: 100%; height: 100%;');
    newRect.classList.add('rounded-xl');
    parent.appendChild(newRect);
}

function CalculateAnalization(type) {
    showLoading();

    const imagePath = 'path/to/your/image.jpg'; 

    fetch('https://python.tptimovyprojekt.software/segmentate_sections', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ path: imagePath })
    })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.polygons && Array.isArray(data.polygons)) {
                appendAnalizedRects(data.polygons);
            } else {
                console.error('Invalid response from server:', data);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error fetching analyzed rectangles:', error);
        });
    }


function appendAnalizedRects(Rects) {
    let parent = document.getElementById('previewContainerAnalyze');
    for (let Rect of Rects) {
        if (Rect.length !== 4) {
            console.error('Invalid Rect:', Rect);
            return;
        }
        let x2 = Rect[0];
        let y2 = Rect[3];
        let x4 = Rect[2];
        let y4 = Rect[1];

        let newRect = document.createElement('segment-rect');
        newRect.setAttribute('x1', Rect[0]);
        newRect.setAttribute('y1', Rect[1]);
        newRect.setAttribute('x2', x2);
        newRect.setAttribute('y2', y2);
        newRect.setAttribute('x3', Rect[2]);
        newRect.setAttribute('y3', Rect[3]);
        newRect.setAttribute('x4', x4);
        newRect.setAttribute('y4', y4);
        newRect.setAttribute('style', 'position: absolute; width: 100%; height: 100%;');
        newRect.classList.add('rounded-xl');
        parent.appendChild(newRect);
    }
}


function CalculateLetters(type) {
    showLoading();

    const imagePath = 'path/to/your/image.jpg';

    fetch('https://python.tptimovyprojekt.software/segmentate_text', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ path: imagePath })
    })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.polygons && Array.isArray(data.polygons)) {
                appendLetterRects(data.polygons);
            } else {
                console.error('Invalid response from server:', data);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error fetching analyzed rectangles:', error);
        });
    }


function appendLetterRects(Rects) {
    let parent = document.getElementById('previewContainerLetter');
    for (let Rect of Rects) {
        if (Rect.length !== 4) {
            console.error('Invalid Rect:', Rect);
            return;
        }
        
        let x2 = Rect[0];
        let y2 = Rect[3];
        let x4 = Rect[2];
        let y4 = Rect[1];

        let newRect = document.createElement('letter-rect');
        newRect.setAttribute('x1', Rect[0]);
        newRect.setAttribute('y1', Rect[1]);
        newRect.setAttribute('x2', x2);
        newRect.setAttribute('y2', y2);
        newRect.setAttribute('x3', Rect[2]);
        newRect.setAttribute('y3', Rect[3]);
        newRect.setAttribute('x4', x4);
        newRect.setAttribute('y4', y4);
        newRect.setAttribute('style', 'position: absolute; width: 100%; height: 100%;');
        newRect.classList.add('rounded-xl');
        parent.appendChild(newRect);
    }
}


function downloadJSON() {
    try {
        const jsonText = document.getElementById("jsonEditor").value;
        const jsonData = JSON.stringify(JSON.parse(jsonText), null, 2);

        const blob = new Blob([jsonData], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        
        // Get filename from input or use default
        const filename = document.getElementById("documentName").value || "document.json";
        
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    } catch (e) {
        alert("Invalid JSON format. Please check your input.");
        console.error("JSON Download Error:", e);
    }
}


function scrollEvent() {
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

function scrollToBookmark(BookmarkId) {
    window.removeEventListener('scroll', scrollEvent);
    document.getElementById(BookmarkId).scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => {
        window.addEventListener('scroll', scrollEvent);
    }, 800);
}

window.addEventListener("beforeunload", function () {
    console.log("Page is being unloaded..., deleting currentImageId:", currentImageId);

    if (currentImageId) {
        deleteUnsavedImage(currentImageId);
    }
});

window.addEventListener('scroll', scrollEvent);

document.getElementById('navbarToggle').addEventListener('click', function () {
    const nav = document.getElementById('navbarNav');
    nav.classList.toggle('hidden');
});

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

hideLoading();
setStep(0);
checkToasts();
