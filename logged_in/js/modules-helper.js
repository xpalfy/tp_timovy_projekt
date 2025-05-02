function filterDocuments() {
    const searchTerm = document.getElementById('documentSearch').value.toLowerCase();
    const filteredDocuments = documentsData.filter(doc => doc.title.toLowerCase().includes(searchTerm));
    return filteredDocuments;
}

function showFilteredDocuments() {
    const filteredDocuments = filterDocuments();
    const documentList = document.getElementById('documentList');
    documentList.innerHTML = '';

    filteredDocuments.forEach(doc => {
        const option = document.createElement('option');
        option.value = doc.id;
        option.textContent = doc.title;
        documentList.appendChild(option);
    });
}

function disableDocumentSearch() {
    document.getElementById('documentSearch').disabled = true;
    document.getElementById('documentSearch').style.pointerEvents = 'none';
}

function showItemSelector() {
    document.getElementById('itemSelector').style.display = 'block';
    hideLoading();
}

function deletePolygons() {
    const parent = document.getElementById('previewContainerLetter');
    const polygons = parent.querySelectorAll('segment-rect');
    polygons.forEach(polygon => {
        parent.removeChild(polygon);
    });
}

function updateImagePreview() {
    const previewImage = document.querySelector('.imagePreview');
    previewImage.src = '../..' + selectedItemImagePath;
    previewImage.style.display = 'block';
}

function hideLoading() {
    loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'none';
    }
}

function showLoading() {
    loadings = document.getElementsByClassName('loading-cont');
    for (let loading of loadings) {
        loading.style.display = 'flex';
    }
}