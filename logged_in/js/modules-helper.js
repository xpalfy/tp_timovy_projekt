export let documentsData = [];
export let selectedDocumentId = null;
export let itemsData = [];
export let selectedItemId = null;
export let selectedItemImagePath = null;

export function goToAnalyzation(doc_id, item_id) {
    window.location.href = 'analyzeModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToLetterSegmentation(doc_id, item_id) {
    window.location.href = 'lettersModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function goToJsonEdit(doc_id, item_id) {
    window.location.href = 'editJsonModule.php?document_id=' + doc_id + '&item_id=' + item_id;
}

export function addNewRect() {
    const parent = document.getElementById('previewContainerSegment');
    const newRect = document.createElement('segment-rect');
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
        token: '<?php echo $_SESSION["token"]; ?>'
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

function getUrlParams() {
    const params = {};
    const queryString = window.location.search.substring(1);
    const vars = queryString.split("&");

    vars.forEach(function (v) {
        const pair = v.split("=");
        params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    });

    return params;
}

export function fetchDocuments() {
    showLoading();

    let data = {
        token: window.phpToken,
        user_id: userData.id,
        status: 'UPLOADED'
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
                    fetchItems(selectedDocumentId, params.item_id);
                }
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Failed to load documents.');
            console.error('Error fetching documents:', error);
        });
}

export function fetchItems(documentId, preselectItemId = null) {
    disableDocumentSearch();
    showItemSelector();

    let data = {
        token: window.phpToken,
        user_id: userData.id,
        document_id: documentId,
        status: 'UPLOADED'
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
            hideLoading();
            console.log('Fetched items:', items);

            $("#itemSelector").empty();
            $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.title;
                document.getElementById('itemSelector').appendChild(option);
            });

            itemsData = items;
            console.log('Items data:', itemsData);

            if (preselectItemId) {
                $("#itemSelector").val(preselectItemId).trigger('change');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Failed to load items.');
            console.error('Error fetching items:', error);
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

export function deletePolygons() {
    const parent = document.getElementById('previewContainerSegment');
    const polygons = parent.querySelectorAll('segment-rect');
    polygons.forEach(polygon => {
        parent.removeChild(polygon);
    });
}

export function showSegmentor() {
    document.getElementById('imageSegmentor').style.display = 'block';
    document.getElementById('loadItemButton').style.display = 'block';
    document.getElementById('addRectButton').style.display = 'block';
}

export function updateImagePreview() {
    const previewImage = document.querySelector('.imagePreview');
    previewImage.src = '../..' + selectedItemImagePath;
    previewImage.style.display = 'block';
}

export function hideLoading() {
    let loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'none';
    }
}

export function showLoading() {
    let loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'flex';
    }
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
            hideLoading();

            if (Array.isArray(data)) {
                data.forEach(segment => {
                    if (segment.polygon && Array.isArray(segment.polygon)) {
                        appendSegmentedRect(segment.polygon, segment.type);
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
            console.error('Error detecting page edges:', error);
        });
}

function appendSegmentedRect(polygon, type) {
    if (polygon.length < 4) {
        console.error('Invalid polygon:', polygon);
        return;
    }

    const parent = document.getElementById('previewContainerSegment');

    const x1 = polygon[0], y1 = polygon[1];
    const x3 = polygon[2], y3 = polygon[3];
    const x2 = x1, y2 = y3;
    const x4 = x3, y4 = y1;

    let newRect = document.createElement('segment-rect');
    newRect.setAttribute('x1', x1);
    newRect.setAttribute('y1', y1);
    newRect.setAttribute('x2', x2);
    newRect.setAttribute('y2', y2);
    newRect.setAttribute('x3', x3);
    newRect.setAttribute('y3', y3);
    newRect.setAttribute('x4', x4);
    newRect.setAttribute('y4', y4);
    newRect.setAttribute('type', type || 'unknown');

    parent.appendChild(newRect);
}