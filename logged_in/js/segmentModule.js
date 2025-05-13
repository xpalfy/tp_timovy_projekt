import { version } from './version.js';

Promise.all([
    import(`./ui-animation-handler.js${version}`),
    import(`./modules-helper.js${version}`)
]).then(([uiAnimationHandlers, modulesHelper]) => {
    const {
        addNewRect, saveSegmentionData, fetchItems,
        fetchDocuments, showProcessingZone, updateImagePreview, deletePolygons, CalculateSegmentation,
        getItemsData, setSelectedItemImagePath, getSelectedItemImagePath,
        getDocumentsData, setSelectedDocumentId, getSelectedDocumentId, setSelectedItemId,
        getSelectedItemId
    } = modulesHelper;
    const {
        setupNavbarToggle, setupGSAPAnimations, setStep, checkToasts, hideLoading, showLoading
    } = uiAnimationHandlers;

    console.log("Modules loaded successfully");


    // Expose functions to the global scope
    window.addNewRect = addNewRect; // Expose addNewRect
    window.saveSegmentionData = saveSegmentionData; // Expose saveSegmentionData
    window.showLoading = showLoading; // Expose showLoading

    if (document.readyState === "complete" || document.readyState === "interactive") {
        initializePage();
    } else {
        document.addEventListener('DOMContentLoaded', initializePage);
    }

    function initializePage() {
        const helpContent = document.getElementById("helpContent");
        const imageZone = document.getElementById("imageSegmentor");
        const bnts = document.getElementById("btns");
        const glass = document.querySelector(".glass");

        imageZone.style.transform = "translateY(-" + helpContent.scrollHeight + "px)";
        bnts.style.transform = "translateY(-" + helpContent.scrollHeight + "px)";

        fetchDocuments('UPLOADED');
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
                fetchItems(getSelectedDocumentId(), null, 'UPLOADED');
            },
            minLength: 1
        });

        $("#itemSelector").change(function () {
            showLoading();
            setSelectedItemId($(this).val());
            setSelectedItemImagePath(getItemsData().find(item => item.id == getSelectedItemId()).image_path);
            showProcessingZone('imageSegmentor');
            updateImagePreview();
            deletePolygons('previewContainerSegment');
            CalculateSegmentation(getSelectedItemImagePath());
            setTimeout(() => {
                glass.style.height = glass.scrollHeight - helpContent.scrollHeight + "px"; 
            }, 100);
            hideLoading();
            
        });
        document.getElementById("helpToggleButton").addEventListener("click", function () {
            this.disabled = true;
            setTimeout(() => {
                this.disabled = false;
            }, 600);
            let fixedHeight = glass.scrollHeight;
            if (helpContent.style.visibility === "hidden" || helpContent.style.visibility === "") {
                helpContent.style.visibility = "visible";
                helpContent.style.animation = "slide-in 0.5s forwards";
                imageZone.style.transform = "translateY(0)";
                bnts.style.transform = "translateY(0)";
                glass.style.height = fixedHeight + helpContent.scrollHeight + 8 + "px";
                this.textContent = "Hide Polygon Help";
            } else {
                helpContent.style.animation = "slide-out 0.5s forwards";
                imageZone.style.transform = "translateY(-"+ helpContent.scrollHeight +"px)";
                bnts.style.transform = "translateY(-" + helpContent.scrollHeight + "px)";
                glass.style.height = fixedHeight - helpContent.scrollHeight + "px";
                setTimeout(() => {
                    helpContent.style.visibility = "hidden";
                }, 500); 
                this.textContent = "Show Polygon Help";
            }
        });
        setupNavbarToggle();
        setupGSAPAnimations();
        hideLoading();
        setStep(1);
        checkToasts();
    }
}).catch(error => {
    console.error("Error loading modules:", error);
});