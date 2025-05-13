import { version } from './version.js';

Promise.all([
    import(`./modules-helper.js${version}`)
]).then(([modulesHelper]) => {
    const {
        goToAnalyzation, addNewRect, saveSegmentionData, fetchItems, showLoading,
        fetchDocuments, showSegmentor, updateImagePreview, deletePolygons, CalculateSegmentation,
        hideLoading, getItemsData, setSelectedItemImagePath, getSelectedItemImagePath,
        getDocumentsData, setSelectedDocumentId, getSelectedDocumentId, setSelectedItemId,
        getSelectedItemId
    } = modulesHelper;

    console.log("Modules loaded successfully");


    // Expose functions to the global scope
    window.goToAnalyzation = goToAnalyzation; // Expose goToAnalyzation
    window.addNewRect = addNewRect; // Expose addNewRect
    window.saveSegmentionData = saveSegmentionData; // Expose saveSegmentionData

    if (document.readyState === "complete" || document.readyState === "interactive") {
        console.log("DOM already loaded before listener registration");
        // Call the function directly if the DOM is already ready
        initializeSegmentModule();
    } else {
        document.addEventListener('DOMContentLoaded', initializeSegmentModule);
    }

    function initializeSegmentModule() {
        console.log("DOM fully loaded and parsed");
        fetchDocuments();
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
                fetchItems(getSelectedDocumentId());
            },
            minLength: 1
        });

        $("#itemSelector").change(function () {
            showLoading();
            setSelectedItemId($(this).val());
            setSelectedItemImagePath(getItemsData().find(item => item.id == getSelectedItemId()).image_path);
            showSegmentor();
            updateImagePreview();
            deletePolygons();
            CalculateSegmentation(getSelectedItemImagePath());
            hideLoading();
        });
    }
}).catch(error => {
    console.error("Error loading modules:", error);
});