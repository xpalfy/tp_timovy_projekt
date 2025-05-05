import { version } from './version.js';

let uiAnimationHandlers;
import(`./ui-animation-handler.js${version}`)
    .then(module => {
        uiAnimationHandlers = module; // Assign the imported module
        console.log("ui-animation-handler module loaded successfully.");
    })
    .catch(error => {
        console.error("Error loading ui-animation-handler module:", error);
    });

// Exported variables
export let doc_id = null;
export let item_id = null;
export let currentImageId = [];
export let previewImages = [];
export let classificationScores = [];
export let currentPreviewIndex = 0;
export let numOfFiles = 0;
export let lastScrollTop = 0;
let image_name = '';
let loadings;

// Functions
export function handleFile(file, shouldShow, first) {
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
export function disableClickUpload() {
    document.getElementById('imageUploader').removeAttribute('onclick');
    document.getElementById('uploadBtn').setAttribute('onclick', 'document.getElementById("fileInput").click()');
}
export function handleDrop(event) {
    event.preventDefault();
    const files = event.dataTransfer.files;
    showLoading();
    numOfFiles = files.length;
    console.log("Number of files dropped:", numOfFiles);
    if (files.length > 1) {
        showScrollBtns();
    } else {
        hideScrollBtns();
    }
    for (let i = 0; i < files.length; i++) {
        handleFile(files[i], i === files.length - 1, i === 0);
    }
}
export function uploadImageButton(event) {
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
export function handleDragOver(event) {
    event.preventDefault();
    document.getElementById('imageUploader').style.border = '4px dashed #eab308';
}
export function handleDragLeave() {
    document.getElementById('imageUploader').style.border = '#eab308 dashed 4px';
}
export function updatePreview() {
    let imageElements = document.getElementsByClassName('imagePreview');
    for (let imageElement of imageElements) {
        if (previewImages.length === 0) {
            imageElement.style.display = 'none';
            continue;
        }
        imageElement.src = previewImages[currentPreviewIndex][0];
        imageElement.style.display = 'block';
    }
}
export function showScrollBtns() {
    let buttons = document.getElementsByClassName('prevBtn');
    for (let button of buttons) {
        button.style.visibility = 'visible';
    }
    buttons = document.getElementsByClassName('nextBtn');
    for (let button of buttons) {
        button.style.visibility = 'visible';
    }
}

export function hideScrollBtns() {
    let buttons = document.getElementsByClassName('prevBtn'); // Declare the variable
    for (let button of buttons) {
        button.style.visibility = 'hidden';
    }
    buttons = document.getElementsByClassName('nextBtn'); // Reassign the variable
    for (let button of buttons) {
        button.style.visibility = 'hidden';
    }
}

function saveData(type) {
    console.log(previewImages);
    if (previewImages.length === 0) {
        uiAnimationHandlers.handleError('Please upload an image first.');
        return;
    }
    let doc_name = document.getElementById('documentName').value;
    /*let json_text = null;
    let decoded_text = null;`
    if (type === 'KEY') {
        json_text = document.getElementById('jsonEditor').value;

        if (json_text === '') {
            uiAnimationHandlers.handleError('Please enter a JSON string.');
            return;
        }
        try {
            JSON.parse(json_text);
        } catch (e) {
            uiAnimationHandlers.handleError('Invalid JSON format. Please check your input.');
            return;
        }
    }
    if (type === 'CIPHER') {
        decoded_text = "Some decoded text"; // <- here call your decoding function later
    }*/

    if (doc_name === '') {
        uiAnimationHandlers.handleError('Please enter a name for the document.');
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
                            //json_text: json_text,
                            //decoded_text: decoded_text,
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
                toastr.success('Document created successfully.');
                uiAnimationHandlers.hideCreateBtns();
                uiAnimationHandlers.showSegmentBtns();
                // reset window
                /*currentImageId = [];
                previewImages = [];
                classificationScores = [];
                currentPreviewIndex = 0;
                updatePreview();
                uiAnimationHandlers.hideCreateBtns();
                uiAnimationHandlers.hideSegmentBtns();
                uiAnimationHandlers.hideAnalyzeKeyBtn();
                uiAnimationHandlers.hideAnalyzeCipherBtn();
                uiAnimationHandlers.hideLettersKeyBtn();
                uiAnimationHandlers.hideLettersCipherBtn();
                uiAnimationHandlers.hideEditJSONKeyBtn();
                uiAnimationHandlers.hideEditJSONCipherBtn();
                uiAnimationHandlers.hideDownloadJSONBtn();
                hideLoading();
                uiAnimationHandlers.hideSystemMessage();
                setStep(0);*/
            } else {
                handleWarning(data.error);
                return;
            }
        });
}

export function saveKey() {
    saveData('KEY');
}

export function saveCipher() {
    saveData('CIPHER');
}

export function saveImage(data, image_name) {
    console.log("Saving image...");
    console.log(data, image_name, window.userData.username, window.userData.id)
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
                    uiAnimationHandlers.showCreateBtns();
                    applyClassificationStyle(classificationScores);
                }
            } else {
                handleError(data.error);
            }
        });
}
export function deleteUnsavedImage(imageId) {
    console.log("Deleting unsaved images...");
    resetClassificationStyle();
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
    uiAnimationHandlers.hideCreateBtns();
    uiAnimationHandlers.hideSegmentBtns();
    uiAnimationHandlers.hideAnalyzeKeyBtn();
    uiAnimationHandlers.hideAnalyzeCipherBtn();
    uiAnimationHandlers.hideLettersKeyBtn();
    uiAnimationHandlers.hideLettersCipherBtn();
    uiAnimationHandlers.hideEditJSONKeyBtn();
    uiAnimationHandlers.hideEditJSONCipherBtn();
    uiAnimationHandlers.hideDownloadJSONBtn();
    hideLoading();
    uiAnimationHandlers.hideSystemMessage();
}

export async function classifyPicture(path) {
    const url = 'https://python.tptimovyprojekt.software/classify';
    console.log("Sending request to Flask server...");

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ path })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
            uiAnimationHandlers.handleError();
        }

        const data = await response.json();

        if (data.classification) {
            console.log("Classification:", data.classification);
            toastr.success(`Classification: ${data.classification}`);
            return data.classification;
        } else {
            console.error("Error in classification response.");
            uiAnimationHandlers.handleError();
        }
    } catch (error) {
        console.error("Error sending request to Flask server:", error.message);
        uiAnimationHandlers.handleError();
    }
}



export function applyClassificationStyle(classification_score) {
    let parentOfBtns = document.getElementById('CreateBtnsBtns');
    let segmentKeyBtn = parentOfBtns.children[0];
    let segmentCipherBtn = parentOfBtns.children[1];
    let messageContainer = document.getElementById('SystemMessage');

    let score = 0;
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
        uiAnimationHandlers.showSystemMessage();
    })
}
function resetClassificationStyle() {
    let parentOfBtns = document.getElementById('CreateBtnsBtns');
    let segmentKeyBtn = parentOfBtns.children[0];
    let segmentCipherBtn = parentOfBtns.children[1];
    let messageContainer = document.getElementById('SystemMessage');

    // Reset styles for buttons by removing the green border
    segmentCipherBtn.style.border = "";
    segmentCipherBtn.style.padding = "";
    segmentKeyBtn.style.border = "";
    segmentKeyBtn.style.padding = "";

    // Clear the message container
    messageContainer.innerHTML = "";
}
export function goToSegmentation() {
    window.location.href = 'modules/segmentModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToAnalyzation(doc_id, item_id) {
    window.location.href = 'analyzeModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToLetterSegmentation(doc_id, item_id) {
    window.location.href = 'lettersModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToJsonEdit(doc_id, item_id) {
        window.location.href = 'editJsonModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}
// etc, keep exporting each function

export function saveProcessing() {
    const jsonText = document.getElementById("jsonEditor").value;
    const jsonData = JSON.stringify(JSON.parse(jsonText), null, 2);

    fetch('modules/saveProcessingResult.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            document_id: doc_id,
            item_id: item_id,
            user_id: window.userData.id,
            status: 'PROCESSED',
            jsonData: jsonData
        })
    }).then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                console.log(data.message);
            } else {
                handleError(data.error);
            }
        });
}

export function downloadJSON() {
    try {
        const jsonText = document.getElementById("jsonEditor").value;
        const jsonData = JSON.stringify(JSON.parse(jsonText), null, 2);

        const blob = new Blob([jsonData], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        const filename = document.getElementById("downloadName").value || "document.json";

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
export function CalculateSegmentation(type) {
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
export function appendSegmentedRect(Rect) {
    if (Rect.length !== 4) {
        console.error('Invalid Rect:', Rect);
        return;
    }

    const parent = document.getElementById('previewContainerSegment');

    const x1 = Rect[0], y1 = Rect[1];
    const x3 = Rect[2], y3 = Rect[3];
    const x2 = x1, y2 = y3;
    const x4 = x3, y4 = y1;

    const xs = [x1, x2, x3, x4];
    const ys = [y1, y2, y3, y4];
    const minX = Math.min(...xs) - 5;
    const minY = Math.min(...ys) - 5;
    const maxX = Math.max(...xs) + 5;
    const maxY = Math.max(...ys) + 5;
    const width = maxX - minX;
    const height = maxY - minY;

    const newRect = document.createElement('segment-rect');
    newRect.setAttribute('x1', x1);
    newRect.setAttribute('y1', y1);
    newRect.setAttribute('x2', x2);
    newRect.setAttribute('y2', y2);
    newRect.setAttribute('x3', x3);
    newRect.setAttribute('y3', y3);
    newRect.setAttribute('x4', x4);
    newRect.setAttribute('y4', y4);

    newRect.style.position = 'absolute';
    newRect.style.left = `0`;
    newRect.style.top = `0`;
    newRect.style.width = `${width}px`;
    newRect.style.height = `${height}px`;

    newRect.classList.add('not-copyable');
    newRect.classList.add('rounded-xl');
    parent.appendChild(newRect);
}


export function CalculateAnalization(type) {
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


export function appendAnalizedRects(Rects) {
    let parent = document.getElementById('previewContainerAnalyze');
    for (let Rect of Rects) {
        if (Rect.length !== 4) {
            console.error('Invalid Rect:', Rect);
            return;
        }

        // Extract points
        const x1 = Rect[0], y1 = Rect[1];
        const x3 = Rect[2], y3 = Rect[3];
        const x2 = x1, y2 = y3;
        const x4 = x3, y4 = y1;

        // Compute bounding box
        const minX = Math.min(x1, x3);
        const minY = Math.min(y1, y3);
        const maxX = Math.max(x1, x3);
        const maxY = Math.max(y1, y3);
        const width = maxX - minX;
        const height = maxY - minY;

        // Create custom element
        let newRect = document.createElement('segment-rect');
        newRect.setAttribute('x1', x1);
        newRect.setAttribute('y1', y1);
        newRect.setAttribute('x2', x2);
        newRect.setAttribute('y2', y2);
        newRect.setAttribute('x3', x3);
        newRect.setAttribute('y3', y3);
        newRect.setAttribute('x4', x4);
        newRect.setAttribute('y4', y4);

        // Set exact position and size
        newRect.style.position = 'absolute';
        newRect.style.left = `0`;
        newRect.style.top = `0`;
        newRect.style.width = `${width}px`;
        newRect.style.height = `${height}px`;

        newRect.classList.add('not-copyable');
        newRect.classList.add('rounded-xl');
        parent.appendChild(newRect);
    }
}



export function CalculateLetters(type) {
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


export function appendLetterRects(Rects) {
    const parent = document.getElementById('previewContainerLetter');

    for (const Rect of Rects) {
        if (Rect.length !== 4) {
            console.error('Invalid Rect:', Rect);
            continue;
        }

        // Rectangle corners from 2 diagonal points
        const x1 = Rect[0], y1 = Rect[1];
        const x3 = Rect[2], y3 = Rect[3];
        const x2 = x1, y2 = y3;
        const x4 = x3, y4 = y1;

        // Bounding box for absolute positioning
        const xs = [x1, x2, x3, x4];
        const ys = [y1, y2, y3, y4];
        const minX = Math.min(...xs) - 5;
        const minY = Math.min(...ys) - 5;
        const maxX = Math.max(...xs) + 5;
        const maxY = Math.max(...ys) + 5;
        const width = maxX - minX;
        const height = maxY - minY;

        // Create and configure custom element
        const newRect = document.createElement('letter-rect');
        newRect.setAttribute('x1', x1);
        newRect.setAttribute('y1', y1);
        newRect.setAttribute('x2', x2);
        newRect.setAttribute('y2', y2);
        newRect.setAttribute('x3', x3);
        newRect.setAttribute('y3', y3);
        newRect.setAttribute('x4', x4);
        newRect.setAttribute('y4', y4);

        // Set proper style (based on bounding box)
        newRect.style.position = 'absolute';
        newRect.style.left = `0`;
        newRect.style.top = `0`;
        newRect.style.width = `${width}px`;
        newRect.style.height = `${height}px`;

        newRect.classList.add('not-copyable');
        newRect.classList.add('rounded-xl');
        parent.appendChild(newRect);
    }
}

export function setupPreviewNavigation() {
    const nextButtons = document.getElementsByClassName('nextBtn');
    for (let button of nextButtons) {
        button.addEventListener('click', function () {
            if (previewImages.length === 0) return;
            currentPreviewIndex = (currentPreviewIndex + 1) % previewImages.length;
            updatePreview();
        });
    }

    const prevButtons = document.getElementsByClassName('prevBtn');
    for (let button of prevButtons) {
        button.addEventListener('click', function () {
            if (previewImages.length === 0) return;
            currentPreviewIndex = (currentPreviewIndex - 1 + previewImages.length) % previewImages.length;
            updatePreview();
        });
    }
}
export function hideLoading() {
    loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'none';
    }
}
export function checkToasts() {
    if (window.toastData) {
        toastr[window.toastData.type](window.toastData.message);
    }
}
export function setStep(index) {
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

function showLoading() {
    loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'flex';
    }
}


