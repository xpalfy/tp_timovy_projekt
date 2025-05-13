export let documentsData = [];
export let selectedDocumentId = null;
export let itemsData = [];
export let selectedItemId = null;
export let selectedItemImagePath = null;

import { version } from './version.js';

import(`./ui-animation-handler.js${version}`)
    .then(module => {
        const { hideLoading, showLoading, handleError, handleWarning } = module;

        window.hideLoading = hideLoading;
        window.showLoading = showLoading;
        window.handleError = handleError;
        window.handleWarning = handleWarning;
    })
    .catch(error => {
        console.error("Error loading ui-animation-handler module:", error);
    });

export function goToAnalyzation(doc_id, item_id) {
    window.location.href = 'analyzeModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToLetterSegmentation(doc_id, item_id) {
    window.location.href = 'lettersModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToJsonEdit(doc_id, item_id) {
    window.location.href = 'editJsonModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function addNewRect(parentName, rect_type = 'segment-rect') {
    const parent = document.getElementById(parentName);
    const newRect = document.createElement(rect_type);
    newRect.setAttribute('x1', 100);
    newRect.setAttribute('y1', 100);
    newRect.setAttribute('x2', 200);
    newRect.setAttribute('y2', 100);
    newRect.setAttribute('x3', 200);
    newRect.setAttribute('y3', 200);
    newRect.setAttribute('x4', 100);
    newRect.setAttribute('y4', 200);
    newRect.setAttribute('type', 'default');

    parent.appendChild(newRect);
}

export function saveSegmentionData() {
    showLoading();
    let rects = document.querySelectorAll('segment-rect');
    let polygons = [];

    rects.forEach(rect => {
        let polygon = [
            { x: rect.getAttribute('x1'), y: rect.getAttribute('y1') },
            { x: rect.getAttribute('x2'), y: rect.getAttribute('y2') },
            { x: rect.getAttribute('x3'), y: rect.getAttribute('y3') },
            { x: rect.getAttribute('x4'), y: rect.getAttribute('y4') },
            { type: rect.getAttribute('type') }
        ];
        polygons.push(polygon);
    });
    console.log('Polygons:', polygons);

    let data = {
        document_id: selectedDocumentId,
        item_id: selectedItemId,
        user_id: userData.id,
        status: 'SEGMENTED',
        polygons: polygons,
        token: window.phpToken
    };

    console.log('Data to be sent:', data);

    fetch('https://python.tptimovyprojekt.software/save_processing_result', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            hideLoading();
            if (data.success) {
                toastr.success('Segmentation data saved successfully.');
                goToAnalyzation(selectedDocumentId, selectedItemId);
            } else {
                toastr.error('Failed to save segmentation data.');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Error saving segmentation data.');
            console.error('Error:', error);
        });
}

export function saveAnalysisData() {
    showLoading();

    let rects = document.querySelectorAll('segment-rect');
    let polygons = [];

    rects.forEach(rect => {
        let polygon = [
            { x: rect.getAttribute('x1'), y: rect.getAttribute('y1') },
            { x: rect.getAttribute('x2'), y: rect.getAttribute('y2') },
            { x: rect.getAttribute('x3'), y: rect.getAttribute('y3') },
            { x: rect.getAttribute('x4'), y: rect.getAttribute('y4') },
            { type: rect.getAttribute('type') }
        ];
        polygons.push(polygon);
    });

    let data = {
        document_id: selectedDocumentId,
        item_id: selectedItemId,
        user_id: userData.id,
        status: 'CLASSIFIED',
        polygons: polygons,
        token: window.phpToken
    };

    console.log('Data to be sent:', data);

    fetch('https://python.tptimovyprojekt.software/save_processing_result', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            hideLoading();
            if (data.success) {
                toastr.success('Analysis data saved successfully.');
                goToLetterSegmentation(selectedDocumentId, selectedItemId);
            } else {
                toastr.error('Failed to save segmentation data.');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Error saving segmentation data.');
            console.error('Error:', error);
        });
}

export function saveLetterData() {
    showLoading();

    let rects = document.querySelectorAll('letter-rect');
    let polygons = [];

    rects.forEach(rect => {
        let polygon = [
            { x: rect.getAttribute('x1'), y: rect.getAttribute('y1') },
            { x: rect.getAttribute('x2'), y: rect.getAttribute('y2') },
            { x: rect.getAttribute('x3'), y: rect.getAttribute('y3') },
            { x: rect.getAttribute('x4'), y: rect.getAttribute('y4') },
            { type: rect.getAttribute('type') }
        ];
        polygons.push(polygon);
    });

    let data = {
        document_id: selectedDocumentId,
        item_id: selectedItemId,
        user_id: userData.id,
        status: 'PROCESSED',
        polygons: polygons,
        token: window.phpToken
    };

    console.log('Data to be sent:', data);

    fetch('https://python.tptimovyprojekt.software/save_processing_result', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            if (data.success) {
                hideLoading();
                toastr.success('Letter segmentation data saved successfully.');
                // call /encode_letters ?? Ez elmenti mint EXTRACTED ami nekunk meg nem kell
                /*
                fetch('https://python.tptimovyprojekt.software/encode_letters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ document_id: selectedDocumentId, user_id: userData.id, token: '<?php echo $_SESSION["token"]; ?>' })

                })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            toastr.success('Letter encoding completed successfully.');
                            goToJsonEdit(selectedDocumentId, selectedItemId);
                        } else {
                            toastr.error('Failed to encode letters.');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        toastr.error('Error encoding letters.');
                        console.error('Error:', error);
                    });
                    */
                goToJsonEdit(selectedDocumentId, selectedItemId);
            } else {
                toastr.error('Failed to save segmentation data.');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Error saving segmentation data.');
            console.error('Error:', error);
        });
}

export function saveJson() {
    showLoading();

    let fixedJson = {
        "alphabet": {
            "a": { "codes": [0, 1, 2] },
            "b": { "codes": [3, 4] },
            "c": { "codes": [5, 6] },
            "d": { "codes": [7, 8] },
            "e": { "codes": [9, 10, 11] },
            "f": { "codes": [12, 13] },
            "g": { "codes": [14, 15] },
            "h": { "codes": [16, 17] },
            "i": { "codes": [18, 19, 20] },
            "k": { "codes": [21, 22] },
            "l": { "codes": [23, 24] },
            "m": { "codes": [25, 26] },
            "n": { "codes": [27, 28] },
            "o": { "codes": [29, 30, 31] },
            "p": { "codes": [32, 33] },
            "q": { "codes": [34, 35] },
            "r": { "codes": [36, 37] },
            "s": { "codes": [38, 39] },
            "t": { "codes": [40, 41] },
            "u": { "codes": [42, 43, 44] },
            "x": { "codes": [45, 46] },
            "y": { "codes": [47, 48] },
            "zeros": { "codes": [49, 50, 51, 52] }
        },
        "doubles": {
            "bb": { "code": 53 },
            "ff": { "code": 54 },
            "ll": { "code": 55 },
            "pp": { "code": 56 },
            "mm": { "code": 57 },
            "nn": { "code": 58 },
            "rr": { "code": 59 },
            "ss": { "code": 60 },
            "tt": { "code": 61 }
        },
        "words": {
            "Papa": { "code": 62 },
            "Rex Ferdinandus": { "code": 63 },
            "Veneti": { "code": 64 },
            "Florentini": { "code": 65 },
            "Dux uh": { "code": 66 },
            "Dux ferrarie": { "code": 67 },
            "Dux urbini": { "code": 68 },
            "Comes bier": { "code": 69 },
            "Cardinales": { "code": 70 },
            "Concilium": { "code": 71 },
            "Genuinfes": { "code": 72 },
            "Maschio mantue": { "code": 73 },
            "Impator": { "code": 74 },
            "Rex hungarie": { "code": 75 },
            "Rex boemie": { "code": 76 },
            "Rex Pollane": { "code": 77 },
            "Dux Saxonie": { "code": 78 },
            "Maschio brandinburgi": { "code": 79 },
            "Dux Sygimundus": { "code": 80 },
            "Dux Burgundie": { "code": 81 },
            "Comes pallatimus": { "code": 82 },
            "Dux baurtie": { "code": 83 },
            "Suyati": { "code": 84 },
            "Soldai": { "code": 85 },
            "La. Na. Os.": { "code": 86 }
        }
    };

    let type = null;
    let status = null;

    if (documentsData.filter(doc => doc.id == selectedDocumentId).length > 0) {
        type = documentsData.filter(doc => doc.id == selectedDocumentId)[0].doc_type;

        if (type === 'KEY') {
            status = 'SAVED';
        } else if (type === 'CIPHER') {
            status = 'EXTRACTED';
        } else {
            toastr.error('Invalid document type');
            return;
        }
    }


    let data = {
        document_id: selectedDocumentId,
        item_id: selectedItemId,
        user_id: userData.id,
        status: status,
        json_data: fixedJson,
        token: window.phpToken
    };

    console.log('Data to be sent:', data);

    fetch('https://python.tptimovyprojekt.software/save_processing_result', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            hideLoading();
            if (data.success) {
                if (type == 'KEY') {
                    window.location.href = `../edit_key/editOwnKeyDocument.php?id=${selectedDocumentId}&user=${userData.id}`;
                } else if (type == 'CIPHER') {
                    window.location.href = `./decipherModule.php?cipher_doc_id=${selectedDocumentId}&cipher_item_id=${selectedItemId}`;
                }
                toastr.success('Letter segmentation data saved successfully.');
            } else {
                toastr.error('Failed to save segmentation data.');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Error saving segmentation data.');
            console.error('Error:', error);
        });
}

export function getUrlParams() {
    const params = {};
    const queryString = window.location.search.substring(1);
    const vars = queryString.split("&");

    vars.forEach(function (v) {
        const pair = v.split("=");
        params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    });

    return params;
}

export function fetchDocuments(status) {
    showLoading();

    let data = {
        token: window.phpToken,
        user_id: userData.id,
        status: status
    };

    fetch('https://python.tptimovyprojekt.software/get_documents_by_user_and_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(docs => {
            hideLoading();
            if (docs.error) {
                throw new Error(docs.error);
            }
            documentsData = docs;

            const params = getUrlParams();
            if (params.document_id) {
                const selectedDoc = documentsData.find(doc => doc.id == params.document_id);
                if (selectedDoc) {
                    selectedDocumentId = selectedDoc.id;
                    $("#documentSearch").val(selectedDoc.title);
                    $("#itemSelector").prop("disabled", false);
                    $("#itemSelector").empty();
                    $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');
                    fetchItems(selectedDocumentId, params.item_id, status);
                }
            }
        })
        .catch(error => {
            hideLoading();
            showNoDocs();
            toastr.error(error || 'Failed to load documents.');
        });
}

function showNoDocs() {
    document.getElementById('noDocs').style.display = 'block';
    document.getElementById('ProcessInfo').style.display = 'none';
    document.getElementById('ProcessInfoMini').style.display = 'none';
    document.getElementById('documentSearch').style.display = 'none';
    document.getElementById('itemSelector').style.display = 'none';
    document.getElementById('helpBtnCont').style.display = 'none';
    document.getElementById('helpContent').style.display = 'none';
    document.getElementById('loadItemButton').style.display = 'none';
    document.getElementById('addRectButton').style.display = 'none';
}

export function fetchItems(documentId, preselectItemId = null, status) {
    disableDocumentSearch();
    showItemSelector();

    let data = {
        token: window.phpToken,
        user_id: userData.id,
        document_id: documentId,
        status: status
    };

    console.log('Requesting items with:', data);

    showLoading();

    fetch('https://python.tptimovyprojekt.software/get_items_by_doc_and_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(items => {
            if (items.error) {
                throw new Error(items.error);
            }
            hideLoading();

            $("#itemSelector").empty();
            $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.title;
                document.getElementById('itemSelector').appendChild(option);
            });

            itemsData = items;

            if (preselectItemId) {
                $("#itemSelector").val(preselectItemId).trigger('change');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Failed to load items.');
        });
}

export function getItemsData() {
    return itemsData;
}

export function getDocumentsData() {
    return documentsData;
}

export function setSelectedDocumentId(docId) {
    selectedDocumentId = docId;
}

export function getSelectedDocumentId() {
    return selectedDocumentId;
}

export function setSelectedItemId(itemId) {
    selectedItemId = itemId;
}

export function getSelectedItemId() {
    return selectedItemId;
}

export function setSelectedItemImagePath(imagePath) {
    selectedItemImagePath = imagePath;
}

export function getSelectedItemImagePath() {
    return selectedItemImagePath;
}

function disableDocumentSearch() {
    document.getElementById('documentSearch').disabled = true;
    document.getElementById('documentSearch').style.pointerEvents = 'none';
}

function showItemSelector() {
    document.getElementById('itemSelector').style.display = 'block';
}

export function deletePolygons(parentName) {
    const parent = document.getElementById(parentName);
    const polygons = parent.querySelectorAll('segment-rect');
    polygons.forEach(polygon => {
        parent.removeChild(polygon);
    });
}

export function showProcessingZone(elementId) {
    document.getElementById('helpBtnCont').style.display = 'flex';
    document.getElementById(elementId).style.display = 'block';
    document.getElementById('loadItemButton').style.display = 'block';
    document.getElementById('addRectButton').style.display = 'block';
}

export function showJsonEditor() {
    document.getElementById('imageJson').style.display = 'block';
    document.getElementById('DownloadJSONBtn').style.display = 'flex';
}

export function updateImagePreview() {
    const previewImage = document.querySelector('.imagePreview');
    previewImage.src = '../..' + selectedItemImagePath;
    previewImage.style.display = 'block';
}

export function fetchJson() {
    const formData = {
        document_id: selectedDocumentId,
        user_id: userData.id,
        token: window.phpToken
            };

    $.ajax({
        url: 'https://python.tptimovyprojekt.software/get_json',
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
            if (res.error) {
                toastr.error(res.error || 'Failed to load key JSON');
            } else {
                $('#jsonEditor').val(JSON.stringify(res, null, 2));
            }
        },
        error: function () {
            toastr.error('Server error while fetching key JSON');
        }
    });
}

export function CalculateSegmentation(imagePath) {
    showLoading();

    fetch('https://python.tptimovyprojekt.software/segmentate_page', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ path: imagePath })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            hideLoading();

            if (Array.isArray(data)) {
                data.forEach(segment => {
                    if (segment.polygon && Array.isArray(segment.polygon)) {
                        appendRects('previewContainerSegment', [segment]);
                    } else {
                        console.warn('Skipping invalid segment:', segment);
                    }
                });
            } else {
                console.error('Invalid response format. Expected array.', data);
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Error detecting edges.');
        });
}

export function CalculateAnalysis(imagePath) {
    showLoading();

    fetch('https://python.tptimovyprojekt.software/segmentate_sections', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ path: imagePath })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            hideLoading();

            if (data && Array.isArray(data.polygons)) {
                appendRects('previewContainerAnalyze', data.polygons); // Access the polygons array
            } else {
                console.error('Invalid response from server:', data);
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Error detecting edges.');
        });
}

export function downloadJSON() {
    try {
        const jsonText = document.getElementById("jsonEditor").value;
        const jsonData = JSON.stringify(JSON.parse(jsonText), null, 2);

        const blob = new Blob([jsonData], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        const filename = documentsData.find(doc => doc.id == selectedDocumentId).title || "document.json";

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

export function CalculateLetters(imagePath) {
    showLoading();

    fetch('https://python.tptimovyprojekt.software/segmentate_text', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ path: imagePath })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            hideLoading();

            if (data && Array.isArray(data.polygons)) {
                appendRects('previewContainerLetter', data.polygons, 'letter-rect'); // Process the polygons array
            } else {
                console.error('Invalid response from server:', data);
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Error detecting letters.');
        });
}

function appendRects(parentName, segments, rect_type = 'segment-rect') {
    const parent = document.getElementById(parentName);

    if (!parent) {
        console.error(`Container with ID "${parentName}" not found.`);
        return;
    }

    for (let segment of segments) {
        const polygon = segment.polygon;
        const type = segment.type || 'unknown';

        if (!polygon || polygon.length < 4) {
            console.error('Invalid polygon:', segment);
            continue;
        }

        const x1 = polygon[0], y1 = polygon[1];
        const x3 = polygon[2], y3 = polygon[3];
        const x2 = x1, y2 = y3;
        const x4 = x3, y4 = y1;

        let newRect = document.createElement(rect_type);
        newRect.setAttribute('x1', x1);
        newRect.setAttribute('y1', y1);
        newRect.setAttribute('x2', x2);
        newRect.setAttribute('y2', y2);
        newRect.setAttribute('x3', x3);
        newRect.setAttribute('y3', y3);
        newRect.setAttribute('x4', x4);
        newRect.setAttribute('y4', y4);
        newRect.setAttribute('type', type);

        parent.appendChild(newRect);
    }
}
