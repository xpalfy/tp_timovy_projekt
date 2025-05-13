import { version } from './version.js';

Promise.all([
    import(`./ui-animation-handler.js${version}`),
    import(`./modules-helper.js${version}`)
]).then(([uiAnimationHandlers, modulesHelper]) => {
    const {
        goToAnalyzation, addNewRect, saveAnalysisData, fetchItems,
        fetchDocuments, showAnalyzer, updateImagePreview, deletePolygons, CalculateAnalysis,
        getItemsData, setSelectedItemImagePath, getSelectedItemImagePath,
        getDocumentsData, setSelectedDocumentId, getSelectedDocumentId, setSelectedItemId,
        getSelectedItemId
    } = modulesHelper;
    const {
        setupNavbarToggle, setupGSAPAnimations, setStep, checkToasts, hideLoading, showLoading
    } = uiAnimationHandlers;

    console.log("Modules loaded successfully");


    // Expose functions to the global scope
    window.goToAnalyzation = goToAnalyzation; // Expose goToAnalyzation
    window.addNewRect = addNewRect; // Expose addNewRect
    window.saveAnalysisData = saveAnalysisData; // Expose saveSegmentionData
    window.showLoading = showLoading; // Expose showLoading

    if (document.readyState === "complete" || document.readyState === "interactive") {
        initializePage();
    } else {
        document.addEventListener('DOMContentLoaded', initializePage);
    }

    function initializePage() {
        fetchDocuments('SEGMENTED');
        $("#documentSearch").autocomplete({
            source: function (request, response) {
                const term = request.term.toLowerCase();
                const filtered = getDocumentsData().filter(doc => doc.title.toLowerCase().includes(term));
                response(filtered.map(doc => ({
                    label: doc.title + ' (' + doc.doc_type.toLowerCase() + ')',
                    value: doc.title,
                    id: doc.id
                })));
            },
            select: function (event, ui) {
                setSelectedDocumentId(ui.item.id);
                $("#itemSelector").prop("disabled", false);
                $("#itemSelector").empty();
                $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');
                fetchItems(getSelectedDocumentId(), null, 'SEGMENTED');
            },
            minLength: 1
        });

        $("#itemSelector").change(function () {
            showLoading();
            setSelectedItemId($(this).val());
            setSelectedItemImagePath(getItemsData().find(item => item.id == getSelectedItemId()).image_path);
            showAnalyzer();
            updateImagePreview();
            deletePolygons('previewContainerAnalyze');
            CalculateAnalysis(getSelectedItemImagePath());
            hideLoading();
        });
        setupNavbarToggle();
        setupGSAPAnimations();
        hideLoading();
        setStep(2);
        checkToasts();
    }
}).catch(error => {
    console.error("Error loading modules:", error);
});