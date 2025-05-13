import { version } from './version.js';

Promise.all([
    import(`./ui-animation-handler.js${version}`),
    import(`./decrypt-helper.js${version}`)
]).then(([uiAnimationHandlers, decryptHelper]) => {
    const {
        fetchItems, fetchKeys, setSelectedCipherItemId, setSelectedKeyItemId,
        setSelectedKeyDocumentId, showSelectedItem, getCipherDocumentsData, getKeyDocumentsData, startDecipher,
        setSelectedCipherDocumentId, getSelectedKeyDocumentId, getSelectedCipherDocumentId,
        fetchDocuments, selectKey, copyToClipboard
    } = decryptHelper;
    const {
        setupNavbarToggle, setupGSAPAnimations, checkToasts, hideLoading, showLoading
    } = uiAnimationHandlers;

    console.log("Modules loaded successfully");


    // Expose functions to the global scope
    window.showLoading = showLoading; // Expose showLoading
    window.selectKey = selectKey; // Expose selectKey
    window.startDecipher = startDecipher; // Expose startDecipher
    window.copyToClipboard = copyToClipboard; // Expose copyToClipboard

    if (document.readyState === "complete" || document.readyState === "interactive") {
        initializePage();
    } else {
        document.addEventListener('DOMContentLoaded', initializePage);
    }

    function initializePage() {
        fetchDocuments();
        $("#documentSearchKey").autocomplete({
            source: function (request, response) {
                const term = request.term.toLowerCase();
                const filtered = getKeyDocumentsData().filter(doc => doc.title.toLowerCase().includes(term) && doc.doc_type === 'KEY');
                response(filtered.map(doc => ({
                    label: doc.title,
                    value: doc.title,
                    id: doc.id
                })));
            },
            select: function (event, ui) {
                setSelectedKeyDocumentId(ui.item.id);
                $("#itemSelectorKey").prop("disabled", false);
                $("#itemSelectorKey").empty();
                $("#itemSelectorKey").append('<option value="" disabled selected>Select an item</option>');
                fetchItems(getSelectedKeyDocumentId(), null, "KEY");
            },
            minLength: 1
        });
        $("#documentSearchCipher").autocomplete({
            source: function (request, response) {
                const term = request.term.toLowerCase();
                const filtered = getCipherDocumentsData().filter(doc => doc.title.toLowerCase().includes(term) && doc.doc_type === 'CIPHER');
                response(filtered.map(doc => ({
                    label: doc.title,
                    value: doc.title,
                    id: doc.id
                })));
            },
            select: function (event, ui) {
                setSelectedCipherDocumentId(ui.item.id);
                $("#itemSelectorCipher").prop("disabled", false);
                $("#itemSelectorCipher").empty();
                $("#itemSelectorCipher").append('<option value="" disabled selected>Select an item</option>');
                fetchItems(getSelectedCipherDocumentId(), null, "CIPHER");
            },
            minLength: 1
        });

        $("#itemSelectorKey").change(function () {
            showLoading();
            setSelectedKeyItemId($(this).val());
            showSelectedItem("KEY");
            document.getElementById('KeySelector').style.display = 'none';
            document.getElementById('recommendMessage').style.display = 'none';
            document.getElementById('startDecipherBtn').style.display = 'block';
            hideLoading();
        });

        $("#itemSelectorCipher").change(function () {
            showLoading();
            document.getElementById('rightSide').style.display = 'flex';
            fetchDocuments('KEY');
            fetchKeys();
            setSelectedCipherItemId($(this).val());
            showSelectedItem("CIPHER");
            hideLoading();
        });
        setupNavbarToggle();
        setupGSAPAnimations();
        hideLoading();
        checkToasts();
    }
}).catch(error => {
    console.error("Error loading modules:", error);
});