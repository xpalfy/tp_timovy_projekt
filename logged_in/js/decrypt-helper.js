const maxSelectItemSize = 10;
let keyDocumentsData = [];
let cipherDocumentsData = [];
let selectedKeyDocumentId = null;
let selectedCipherDocumentId = null;
let itemsDataKey = [];
let itemsDataCipher = [];
let selectedKeyItemId = null;
let selectedCipherItemId = null;
let selectedItemImagePath = null;

import { hideLoading, showLoading } from './ui-animation-handler.js';
import { getUrlParams } from './modules-helper.js';

export function fetchDocuments(type = 'CIPHER') {

    let status = type === 'KEY' ? 'SAVED' : 'EXTRACTED';

    let data = {
        token: window.phpToken,
        user_id: userData.id,
        status: status
    };

    showLoading();
    fetch('https://python.tptimovyprojekt.software/get_documents_by_user_and_status', { // Itt kéne a public is 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(docs => {
            if (docs.error) {
                throw new Error(docs.error);
            }

            if (type === 'KEY') {
                keyDocumentsData = docs;
            } else if (type === 'CIPHER') {
                cipherDocumentsData = docs;
            }

            hideLoading();
            console.log('Fetched documents:', docs);
            const params = getUrlParams();
            if (params.cipher_doc_id && type === 'CIPHER') {
                const selectedDoc = cipherDocumentsData.find(doc => doc.id == params.cipher_doc_id);
                if (selectedDoc) {
                    selectedCipherDocumentId = selectedDoc.id;
                    $("#documentSearchCipher").val(selectedDoc.title);
                    $("#itemSelectorCipher").prop("disabled", false);
                    $("#itemSelectorCipher").empty();
                    $("#itemSelectorCipher").append('<option value="" disabled selected>Select an item</option>');
                    fetchItems(selectedCipherDocumentId, params.cipher_item_id, "CIPHER");
                }
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Failed to load documents.');
            console.error('Error fetching documents:', error);
        });
}

export function fetchItems(documentId, preselectItemId = null, type) {
    disableDocumentSearch(type);
    showItemSelector(type);

    let status = type === 'KEY' ? 'SAVED' : 'EXTRACTED';

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
            console.log('Fetched items:', items);

            $("#itemSelectorKey").empty();
            $("#itemSelectorKey").append('<option value="" disabled selected>Select an item</option>');

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.title;
                if (item.type == type) {
                    if (type == 'KEY') {
                        document.getElementById('itemSelectorKey').appendChild(option);
                        itemsDataKey.push(item);
                    } else {
                        document.getElementById('itemSelectorCipher').appendChild(option);
                        itemsDataCipher.push(item);
                    }
                }
            });

            if (preselectItemId) {
                if (type == 'KEY') {
                    $("#itemSelectorKey").val(preselectItemId).trigger('change');
                } else {
                    $("#itemSelectorCipher").val(preselectItemId).trigger('change');
                }
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Failed to load items.');
            console.error('Error fetching items:', error);
        });
}

function disableDocumentSearch(type) {
    if (type === 'KEY') {
        document.getElementById('documentSearchKey').disabled = true;
        document.getElementById('documentSearchKey').style.pointerEvents = 'none';
    } else {
        document.getElementById('documentSearchCipher').disabled = true;
        document.getElementById('documentSearchCipher').style.pointerEvents = 'none';
    }
}

function showItemSelector(type) {
    if (type === 'KEY') {
        document.getElementById('itemSelectorKey').style.display = 'block';
    } else {
        document.getElementById('itemSelectorCipher').style.display = 'block';
    }
}

export function fetchKeys() {
    let data = {
        token: window.phpToken,
        user_id: userData.id,
        document_id: selectedCipherDocumentId
    };

    showLoading();
    fetch('https://python.tptimovyprojekt.software/get_keys_for_cipher', {
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
            const keySelector = document.getElementById('KeySelector');
            keySelector.innerHTML = ''; // Clear previous options
            items.forEach(item => {
                const card = document.createElement('div');
                card.className = 'bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-4 m-2 flex flex-col items-center w-48 h-64';

                card.innerHTML = `
        <div class="w-full h-36 flex justify-center items-center overflow-hidden bg-[#f0e7d5] rounded-lg">
            <img src="../..${item.image_path}" alt="${item.title}" class="w-full h-full object-cover">
        </div>
        <p class="mt-2 text-center font-semibold">${item.title}</p>
        <button class="bg-[#d7c7a5] text-[#4b4b4b] border border-[#4b4b4b] rounded-lg p-2 mt-2 transition duration-300 hover:bg-[#c4b59d] hover:text-[#2d2d2d]" onclick="selectKey(${item.document_id})">Select</button>
    `;

                keySelector.appendChild(card);
            });


            if (items.length > maxSelectItemSize) {
                keySelector.style.overflowY = 'scroll';
                keySelector.style.maxHeight = '300px';
            } else {
                keySelector.style.overflowY = 'hidden';
                keySelector.style.maxHeight = 'none';
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Failed to load keys.');
            console.error('Error fetching keys:', error);
        });
}

export function showSelectedItem(type) {
    let selectedItem = null;
    if (type === "KEY") {
        selectedItem = itemsDataKey.find(item => item.id == selectedKeyItemId);
    } else {
        selectedItem = itemsDataCipher.find(item => item.id == selectedCipherItemId);
    }
    if (selectedItem) {
        selectedItemImagePath = '../..' + selectedItem.image_path;
        if (type === "KEY") {
            document.getElementById('imagePreviewKey').src = selectedItemImagePath;
            document.getElementById('imagePreviewKey').style.display = 'block';
            document.getElementById('documentSearchKey').style.display = 'none';
            document.getElementById('itemSelectorKey').style.display = 'none';
        } else {
            document.getElementById('imagePreviewCipher').src = selectedItemImagePath;
            document.getElementById('imagePreviewCipher').style.display = 'block';
            document.getElementById('documentSearchCipher').style.display = 'none';
            document.getElementById('itemSelectorCipher').style.display = 'none';
        }
    }
}

export function selectKey(keyId) {
    selectedKeyDocumentId = keyId;
    const selectedKey = itemsDataKey.find(item => item.document_id == selectedKeyDocumentId);
    if (selectedKey) {
        selectedItemImagePath = '../..' + selectedKey.image_path;
        document.getElementById('imagePreviewKey').src = selectedItemImagePath;
        document.getElementById('imagePreviewKey').style.display = 'block';
        document.getElementById('documentSearchKey').style.display = 'none';
        document.getElementById('itemSelectorKey').style.display = 'none';
        document.getElementById('KeySelector').style.display = 'none';
        document.getElementById('recommendMessage').style.display = 'none';
        document.getElementById('startDecipherBtn').style.display = 'block';
    }
}

export function startDecipher() {
    let data = {
        token: window.phpToken,
        user_id: userData.id,
        cipher_document_id: selectedCipherDocumentId,
        key_document_id: selectedKeyDocumentId,
    };

    console.log('Requesting items with:', data);
    showLoading();

    fetch('https://python.tptimovyprojekt.software/decrypt_cipher_with_key', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(item => {
            if (item.error) {
                throw new Error(item.error);
            }
            hideLoading();
            console.log('Fetched items:', item);
            document.getElementById('startDecipherBtn').style.display = 'none';
            document.getElementById('resultArea').style.display = 'block';
            setDecryptResult(item.decrypted);
            saveResult(item);
        })
        .catch(error => {
            hideLoading();
            toastr.error('Failed to decrypt items.');
            console.error('Error fetching items:', error);
        });
}

function saveResult(decryptResult) {
    let data = {
        token: window.phpToken,
        user_id: userData.id,
        document_id: selectedCipherDocumentId,
        item_id: selectedCipherItemId,
        status: 'SAVED',
        json_data: decryptResult
    };

    console.log('Requesting items with:', data);
    showLoading();

    fetch('https://python.tptimovyprojekt.software/save_processing_result', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(item => {
            if (item.error) {
                throw new Error(item.error);
            }
            hideLoading();
            console.log('Fetched items:', item);
            toastr.success('Decrypted text saved successfully.');
            showNavigationBtn();
        })
        .catch(error => {
            hideLoading();
            toastr.error(error || 'Failed to save decrypted text.');
            console.error('Error fetching items:', error);
        });
}

function showNavigationBtn() {
    const navigationBtn = document.getElementById('navigationBtn');
    navigationBtn.style.display = 'block';
}

function setDecryptResult(decryptResult) {
    const resultText = document.getElementById('resultText');
    resultText.innerHTML = decryptResult;
}

export function copyToClipboard() {
    const resultText = document.getElementById('resultText');
    resultText.select();
    document.execCommand("copy");
    toastr.success('Copied to clipboard!');
}

export function setSelectedCipherItemId(itemId) {
    selectedCipherItemId = itemId;
}

export function setSelectedKeyItemId(itemId) {
    selectedKeyItemId = itemId;
}
export function setSelectedKeyDocumentId(docId) {
    selectedKeyDocumentId = docId;
}
export function setSelectedCipherDocumentId(docId) {
    selectedCipherDocumentId = docId;
}
export function getSelectedKeyDocumentId() {
    return selectedKeyDocumentId;
}
export function getSelectedCipherDocumentId() {
    return selectedCipherDocumentId;
}
export function getSelectedKeyItemId() {
    return selectedKeyItemId;
}
export function getSelectedCipherItemId() {
    return selectedCipherItemId;
}

export function getKeyDocumentsData() {
    return keyDocumentsData;
}

export function getCipherDocumentsData() {
    return cipherDocumentsData;
}