class PolygonElement extends HTMLElement {
    static get observedAttributes() {
        return ['x1', 'y1', 'x2', 'y2', 'x3', 'y3', 'x4', 'y4', 'type'];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.draggingPoint = null;
        this.onMouseMove = this.onMouseMove.bind(this);
        this.onMouseUp = this.onMouseUp.bind(this);
    }

    connectedCallback() {
        this.render();
        this.shadowRoot.addEventListener('mousedown', this.onMouseDown.bind(this));
        document.addEventListener('mousemove', this.onMouseMove.bind(this));
        document.addEventListener('mouseup', this.onMouseUp.bind(this));
        this.shadowRoot.addEventListener('mouseover', this.onHover.bind(this));
        this.shadowRoot.addEventListener('mouseleave', this.onLeave.bind(this));
    }

    disconnectedCallback() {
        document.removeEventListener('mousemove', this.onMouseMove);
        document.removeEventListener('mouseup', this.onMouseUp);
    }

    attributeChangedCallback(name, oldValue, newValue) {
        this.render();
    }

    onMouseDown(event) {
        const point = event.target;
        if (point.tagName === 'circle') {
            this.draggingPoint = point;
        }
        if (point.tagName === 'polygon') {
            this.showDetails();
        }
    }

    onHover(event) {
        if (event.target.tagName === 'polygon') {
            event.target.style.cursor = 'pointer';
        }
        if (event.target.tagName === 'circle') {
            event.target.style.cursor = 'move';
        }
    }
    onLeave(event) {
        if (event.target.tagName === 'polygon' || event.target.tagName === 'circle') {
            event.target.style.cursor = 'default';
        }
    }

    onMouseMove(event) {
        if (this.draggingPoint) {
            const parentRect = this.parentElement.getBoundingClientRect();
            const x = Math.min(Math.max(event.clientX - parentRect.left, 3), parentRect.width - 3);
            const y = Math.min(Math.max(event.clientY - parentRect.top, 3), parentRect.height - 3);

            this.draggingPoint.setAttribute('cx', x);
            this.draggingPoint.setAttribute('cy', y);
            this.updatePolygonPoints();
        }
    }

    onMouseUp() {
        this.draggingPoint = null;
        this.checkAndFixCrossing();
    }

    updatePolygonPoints() {
        const points = Array.from(this.shadowRoot.querySelectorAll('circle')).map(circle => {
            return `${circle.getAttribute('cx')},${circle.getAttribute('cy')}`;
        }).join(' ');
        this.shadowRoot.querySelector('polygon').setAttribute('points', points);
    }

    showDetails() {
        const type = this.getAttribute('type');
        const coords = ['x1', 'y1', 'x2', 'y2', 'x3', 'y3', 'x4', 'y4'].map(attr =>
            parseFloat(this.getAttribute(attr))
        );
        const [x1, y1, x2, y2, x3, y3, x4, y4] = coords;

        const xs = [x1, x2, x3, x4];
        const ys = [y1, y2, y3, y4];
        const minX = Math.min(...xs);
        const minY = Math.min(...ys);
        const maxX = Math.max(...xs);
        const maxY = Math.max(...ys);
        const width = maxX - minX;
        const height = maxY - minY;

        let current = this.parentElement;
        let img = null;
        while (current && !img) {
            img = current.querySelector('img');
            current = current.parentElement;
        }

        if (!img) {
            console.error('No image found for cropping.');
            return;
        }

        const renderedWidth = this.offsetParent.offsetWidth;
        const scaleX = img.naturalWidth / renderedWidth;
        const scaleY = img.naturalHeight / img.offsetHeight;

        const realMinX = minX * scaleX;
        const realMinY = minY * scaleY;
        const realWidth = width * scaleX;
        const realHeight = height * scaleY;

        const canvas = document.createElement('canvas');
        canvas.width = realWidth;
        canvas.height = realHeight;
        const ctx = canvas.getContext('2d');

        if (!img.complete) {
            img.onload = () => this.extractAndShow(ctx, img, realMinX, realMinY, realWidth, realHeight, canvas, type, coords);
        } else {
            this.extractAndShow(ctx, img, realMinX, realMinY, realWidth, realHeight, canvas, type, coords);
        }
    }


    extractAndShow(ctx, img, x, y, w, h, canvas, type, coords) {
        ctx.drawImage(img, x, y, w, h, 0, 0, w, h);

        const modal = document.getElementById('polygonModal');
        const modalContent = document.getElementById('polygonModalContent');
        modalContent.style.position = 'relative';
        modalContent.style.marginBottom = '45px';

        const typeOptions = ['default', 'page', 'word', 'alphabet', 'null', 'double'];

        modalContent.innerHTML = `
            <h3>Rectangle Details</h3>
            <p><strong>Type:</strong>
                <select id="typeSelect">
                    ${typeOptions.map(opt => 
                        `<option value="${opt}" ${opt === type ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            </p>
            <p><strong>Coordinates:</strong></p>
            <ul>
              <li>P1: (${coords[0]}, ${coords[1]})</li>
              <li>P2: (${coords[2]}, ${coords[3]})</li>
              <li>P3: (${coords[4]}, ${coords[5]})</li>
              <li>P4: (${coords[6]}, ${coords[7]})</li>
            </ul>
            <p><strong>Extracted region:</strong></p>
            <img src="${canvas.toDataURL()}" alt="Cropped image" style="width: 100%; height: 100%; max-width: 500px; max-height: 300px;" />
            <button id="deleteButton" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded shadow transition ml-2" style="position: absolute; right:-18px; bottom:-53px">Delete</button>
        `;
        modal.style.display = 'flex';
        const deleteButton = modalContent.querySelector('#deleteButton');
        deleteButton.addEventListener('click', () => {
            this.parentElement.removeChild(this);
            modal.style.display = 'none';
        });
    
        const typeSelect = modalContent.querySelector('#typeSelect');
        typeSelect.addEventListener('change', (e) => {
            this.setAttribute('type', e.target.value);
            modal.style.display = 'none';
        });
    }

    checkAndFixCrossing() {
        const circles = Array.from(this.shadowRoot.querySelectorAll('circle'));
        let pointsArray = circles.map(circle => ({
            x: parseFloat(circle.getAttribute('cx')),
            y: parseFloat(circle.getAttribute('cy'))
        }));

        function doLinesIntersect(p1, p2, p3, p4) {
            function ccw(a, b, c) {
                return (c.y - a.y) * (b.x - a.x) > (b.y - a.y) * (c.x - a.x);
            }
            return (
                ccw(p1, p3, p4) !== ccw(p2, p3, p4) &&
                ccw(p1, p2, p3) !== ccw(p1, p2, p4)
            );
        }

        const [A, B, C, D] = pointsArray;

        if (doLinesIntersect(A, B, C, D) || doLinesIntersect(A, D, B, C)) {

            let centroid = {
                x: (A.x + B.x + C.x + D.x) / 4,
                y: (A.y + B.y + C.y + D.y) / 4
            };

            pointsArray.sort((a, b) => {
                let angleA = Math.atan2(a.y - centroid.y, a.x - centroid.x);
                let angleB = Math.atan2(b.y - centroid.y, b.x - centroid.x);
                return angleA - angleB;
            });

            pointsArray.forEach((point, index) => {
                circles[index].setAttribute('cx', point.x);
                circles[index].setAttribute('cy', point.y);
                this.setAttribute(`x${index + 1}`, point.x);
                this.setAttribute(`y${index + 1}`, point.y);
            });

            const newPoints = pointsArray.map(p => `${p.x},${p.y}`).join(' ');
            this.shadowRoot.querySelector('polygon').setAttribute('points', newPoints);
        } else {
            pointsArray.forEach((point, index) => {
                this.setAttribute(`x${index + 1}`, point.x);
                this.setAttribute(`y${index + 1}`, point.y);
            });
        }
    }

    getColorFromType(type) {
        switch (type) {
            case 'page':
                return '#007bff';
            case 'alphabet':
                return '#28a745';
            case 'null':
                return '#dc3545';
            case 'double':
                return '#ffc107';
            case 'word':
                return '#17a2b8';
            default:
                return '#6c757d';
        }
    }

    checkType(type) {
        const types = ['page', 'alphabet', 'null', 'double', 'word'];
        if (types.includes(type)) {
            return type;
        } else {
            return 'default';
        }

    }

    render() {
        const x1 = parseFloat(this.getAttribute('x1')) || 0;
        const y1 = parseFloat(this.getAttribute('y1')) || 0;
        const x2 = parseFloat(this.getAttribute('x2')) || 0;
        const y2 = parseFloat(this.getAttribute('y2')) || 0;
        const x3 = parseFloat(this.getAttribute('x3')) || 0;
        const y3 = parseFloat(this.getAttribute('y3')) || 0;
        const x4 = parseFloat(this.getAttribute('x4')) || 0;
        const y4 = parseFloat(this.getAttribute('y4')) || 0;
        const color = this.getColorFromType(this.checkType(this.getAttribute('type')));


        const width = this.clientWidth || 100;
        const height = this.clientHeight || 100;


        this.shadowRoot.innerHTML = `
            <svg 
                width="${width}" 
                height="${height}" 
                viewBox="0 0 ${width} ${height}" 
                style="overflow: visible; pointer-events: none; position: absolute; left: 0; top: 0;"
            >
                <polygon points="${x1},${y1} ${x2},${y2} ${x3},${y3} ${x4},${y4}" 
                         style="fill:${color}1c;stroke:black;stroke-width:1; pointer-events: auto;" />
                <circle cx="${x1}" cy="${y1}" r="3" fill="${color}" style="pointer-events: auto;" />
                <circle cx="${x2}" cy="${y2}" r="3" fill="${color}" style="pointer-events: auto;" />
                <circle cx="${x3}" cy="${y3}" r="3" fill="${color}" style="pointer-events: auto;" />
                <circle cx="${x4}" cy="${y4}" r="3" fill="${color}" style="pointer-events: auto;" />
            </svg>
        `;
    }
}

customElements.define('letter-rect', PolygonElement);
