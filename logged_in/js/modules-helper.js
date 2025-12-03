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

    fetch('https://python.egytolnyolcig.uk/documents/save_processing_result', {
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

    fetch('https://python.egytolnyolcig.uk/documents/save_processing_result', {
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

    fetch('https://python.egytolnyolcig.uk/documents/save_processing_result', {
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
                fetch('https://python.egytolnyolcig.uk/modules/encode_letters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ document_id: selectedDocumentId, user_id: userData.id, token: window.phpToken })
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
        "keys": {
            "alphabet": {
            "/x54321098": "b",
            "/x98765410": "t",
            "/x20098721": "n",
            "/x15043176": "k",
            "/x12019843": "x"
            },
            "doubles": {
            "/x32109876": "ff",
            "/x76543210": "ll",
            "/x32919854": "ss",
            "/x65943187": "zz"
            },
            "words": {
            "/x21098765": "Aurelix",
            "/x43210987": "Zenithor",
            "/x65432109": "Tarnum Rex",
            "/x87654300": "Quantoris",
            "/x09876521": "zeros",
            "/x10987632": "Dux Silvestris",
            "/x21908743": "Virellum",
            "/x43920965": "Papa Oranus",
            "/x54932076": "Nostrix",
            "/x76954298": "Florani",
            "/x98976510": "Umbrax",
            "/x09987621": "Velentor",
            "/x11098732": "Dux Aquarum",
            "/x13020954": "Zentauri",
            "/x14032065": "Ortanum",
            "/x16054287": "Lunaris",
            "/x17065398": "umel",
            "/x18076509": "Cestria",
            "/x19087610": "Quorvus",
            "/x21109832": "Praetor Nova"
            }
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

    fetch('https://python.egytolnyolcig.uk/documents/save_processing_result', {
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

    fetch('https://python.egytolnyolcig.uk/documents/get_documents_by_user_and_status', {
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

    fetch('https://python.egytolnyolcig.uk/documents/get_items_by_doc_and_status', {
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
        url: 'https://python.egytolnyolcig.uk/documents/get_json',
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

function calculateImageScale() {
    const previewImage = document.querySelector('.imagePreview');
    const imageWidth = previewImage.naturalWidth;
    const imageHeight = previewImage.naturalHeight;
    const containerWidth = previewImage.clientWidth;
    const containerHeight = previewImage.clientHeight;
    const scaleX = containerWidth / imageWidth;
    const scaleY = containerHeight / imageHeight;
    return [scaleX, scaleY];
}

export function CalculateSegmentation(imagePath) {
    showLoading();

    fetch('https://python.egytolnyolcig.uk/modules/segmentate_page', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Caller-Url': window.fullCallerUrl,
        },
        body: JSON.stringify({ path: imagePath})
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
                        appendRects('previewContainerSegment', [segment], 'segment-rect', calculateImageScale());
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

    fetch('https://python.egytolnyolcig.uk/modules/segmentate_sections', {
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
                appendRects('previewContainerAnalyze', data.polygons, 'segment-rect'); // TODO: add scale if Yolo model is used
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

    fetch('https://python.egytolnyolcig.uk/segmentate_text', {
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
                appendRects('previewContainerLetter', data.polygons, 'letter-rect'); // TODO: add scale if Yolo model is used
            } else {
                console.error('Invalid response from server:', data);
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Error detecting letters.');
        });
}

function appendRects(parentName, segments, rect_type = 'segment-rect', scale = [1, 1]) {
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

        const x1 = polygon[0] * scale[0], y1 = polygon[1] * scale[1];
        const x3 = polygon[2] * scale[0], y3 = polygon[3] * scale[1];
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
