class PolygonElement extends HTMLElement {
    static get observedAttributes() {
        return ['x1', 'y1', 'x2', 'y2', 'x3', 'y3', 'x4', 'y4', 'type'];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.draggingPoint = null;
        this.draggingPolygon = false;
        this.dragStartX = 0;
        this.dragStartY = 0;
        this.initialPoints = [];
        this.onMouseMove = this.onMouseMove.bind(this);
        this.onMouseUp = this.onMouseUp.bind(this);
    }

    connectedCallback() {
        this.render();
        this.mouseDownListener = this.onMouseDown.bind(this);
        this.mouseOverListener = this.onHover.bind(this);
        this.mouseLeaveListener = this.onLeave.bind(this);

        this.shadowRoot.addEventListener('mousedown', this.mouseDownListener);
        this.shadowRoot.addEventListener('mouseover', this.mouseOverListener);
        this.shadowRoot.addEventListener('mouseleave', this.mouseLeaveListener);

        // Store bound functions so we can remove them later
        this.boundMouseMove = this.onMouseMove.bind(this);
        this.boundMouseUp = this.onMouseUp.bind(this);
        document.addEventListener('mousemove', this.boundMouseMove);
        document.addEventListener('mouseup', this.boundMouseUp);
    }

    disconnectedCallback() {
        // Clean up all event listeners
        this.shadowRoot.removeEventListener('mousedown', this.mouseDownListener);
        this.shadowRoot.removeEventListener('mouseover', this.mouseOverListener);
        this.shadowRoot.removeEventListener('mouseleave', this.mouseLeaveListener);
        document.removeEventListener('mousemove', this.boundMouseMove);
        document.removeEventListener('mouseup', this.boundMouseUp);

        // Clear any dragging state
        this.draggingPoint = null;
        this.draggingPolygon = false;
    }

    attributeChangedCallback(name, oldValue, newValue) {
        this.render();
    }

    onMouseDown(event) {
        const point = event.target;

        if (point.tagName === 'circle') {
            this.draggingPoint = point;
            this.draggingPointIndex = Array.from(this.shadowRoot.querySelectorAll('circle')).indexOf(point);
            // Store initial positions of all points
            this.initialPoints = Array.from(this.shadowRoot.querySelectorAll('circle')).map(circle => ({
                x: parseFloat(circle.getAttribute('cx')),
                y: parseFloat(circle.getAttribute('cy'))
            }));
            return;
        }

        if (point.tagName === 'polygon') {
            // Store initial positions and mouse position
            this.draggingPolygon = true;
            this.dragStartX = event.clientX;
            this.dragStartY = event.clientY;
            this.initialPoints = Array.from(this.shadowRoot.querySelectorAll('circle')).map(circle => ({
                x: parseFloat(circle.getAttribute('cx')),
                y: parseFloat(circle.getAttribute('cy'))
            }));

            // Store the time when mouse down occurred
            this.mouseDownTime = Date.now();
            event.preventDefault();
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
        // Check if element is still connected to DOM
        if (!this.isConnected) {
            this.disconnectedCallback();
            return;
        }

        // Check if parent element exists
        if (!this.parentElement) {
            return;
        }

        try {
            const parentRect = this.parentElement.getBoundingClientRect();
            const x = event.clientX - parentRect.left;
            const y = event.clientY - parentRect.top;

            if (this.draggingPoint) {
                const clampedX = Math.min(Math.max(x, 7), parentRect.width - 7);
                const clampedY = Math.min(Math.max(y, 7), parentRect.height - 7);

                // Update the dragged point
                this.draggingPoint.setAttribute('cx', clampedX);
                this.draggingPoint.setAttribute('cy', clampedY);

                // Get all circles
                const circles = Array.from(this.shadowRoot.querySelectorAll('circle'));

                // Calculate which points need to move to maintain rectangle shape
                switch (this.draggingPointIndex) {
                    case 0: // Top-left corner
                        circles[3].setAttribute('cy', clampedY); // Top-right (same Y)
                        circles[1].setAttribute('cx', clampedX); // Bottom-left (same X)
                        break;
                    case 3: // Top-right corner
                        circles[0].setAttribute('cy', clampedY); // Top-left (same Y)
                        circles[2].setAttribute('cx', clampedX); // Bottom-right (same X)
                        break;
                    case 2: // Bottom-right corner
                        circles[1].setAttribute('cy', clampedY); // Bottom-left (same Y)
                        circles[3].setAttribute('cx', clampedX); // Top-right (same X)
                        break;
                    case 1: // Bottom-left corner
                        circles[2].setAttribute('cy', clampedY); // Bottom-right (same Y)
                        circles[0].setAttribute('cx', clampedX); // Top-left (same X)
                        break;
                }

                this.updatePolygonPoints();
            } else if (this.draggingPolygon) {
                // Calculate how far we've moved
                const dx = Math.abs(event.clientX - this.dragStartX);
                const dy = Math.abs(event.clientY - this.dragStartY);

                // Only consider it a drag if we've moved more than 5 pixels
                if (dx > 5 || dy > 5) {
                    // This is definitely a drag, not a click
                    const newX = event.clientX - parentRect.left;
                    const newY = event.clientY - parentRect.top;

                    const offsetX = newX - (this.dragStartX - parentRect.left);
                    const offsetY = newY - (this.dragStartY - parentRect.top);

                    const circles = Array.from(this.shadowRoot.querySelectorAll('circle'));
                    circles.forEach((circle, index) => {
                        const newCX = Math.min(Math.max(this.initialPoints[index].x + offsetX, 7), parentRect.width - 7);
                        const newCY = Math.min(Math.max(this.initialPoints[index].y + offsetY, 7), parentRect.height - 7);

                        circle.setAttribute('cx', newCX);
                        circle.setAttribute('cy', newCY);
                    });

                    this.updatePolygonPoints();
                    this.dragStartX = event.clientX;
                    this.dragStartY = event.clientY;
                    this.initialPoints = circles.map(circle => ({
                        x: parseFloat(circle.getAttribute('cx')),
                        y: parseFloat(circle.getAttribute('cy'))
                    }));
                }
            }
        } catch (error) {
            console.error('Error in onMouseMove:', error);
            this.disconnectedCallback();
        }
    }

    onMouseUp(event) {
        if (this.draggingPoint || this.draggingPolygon) {
            // Check if this was a click (minimal movement) and not a drag
            const isClick = this.draggingPolygon &&
                Math.abs(event.clientX - this.dragStartX) <= 5 &&
                Math.abs(event.clientY - this.dragStartY) <= 5 &&
                (Date.now() - this.mouseDownTime) < 100; // Less than 200ms

            if (isClick) {
                // It was a click, show the modal
                this.showDetails();
            } else {
                // It was a drag, update attributes
                const circles = Array.from(this.shadowRoot.querySelectorAll('circle'));
                circles.forEach((circle, index) => {
                    this.setAttribute(`x${index + 1}`, circle.getAttribute('cx'));
                    this.setAttribute(`y${index + 1}`, circle.getAttribute('cy'));
                });

                this.checkAndFixCrossing();
            }

            this.draggingPoint = null;
            this.draggingPolygon = false;
        }
    }

    updatePolygonPoints() {
        const points = Array.from(this.shadowRoot.querySelectorAll('circle')).map(circle => {
            return `${circle.getAttribute('cx')},${circle.getAttribute('cy')}`;
        }).join(' ');
        this.shadowRoot.querySelector('polygon').setAttribute('points', points);
    }

    showDetails() {
        const type = this.getAttribute('type');
        const x1 = this.getAttribute('x1');
        const y1 = this.getAttribute('y1');
        const x2 = this.getAttribute('x2');
        const y2 = this.getAttribute('y2');
        const x3 = this.getAttribute('x3');
        const y3 = this.getAttribute('y3');
        const x4 = this.getAttribute('x4');
        const y4 = this.getAttribute('y4');

        const typeOptions = ['default', 'page', 'word', 'alphabet', 'null', 'double'];

        const content = `
            <h3>Rectangle Details</h3>
            <p><strong>Type:</strong>
                <select id="typeSelect">
                    ${typeOptions.map(opt =>
            `<option value="${opt}" ${opt === type ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            </p>
            <p><strong>Coordinates:</strong></p>
            <ul>
                <li>P1: (${x1}, ${y1})</li>
                <li>P2: (${x2}, ${y2})</li>
                <li>P3: (${x3}, ${y3})</li>
                <li>P4: (${x4}, ${y4})</li>
            </ul>
            <button id="deleteButton" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded shadow transition ml-2" style="position: absolute; right:-18px; bottom:-9px">Delete</button>
        `;

        const modal = document.getElementById('polygonModal');
        const modalContent = document.getElementById('polygonModalContent');
        modalContent.style.position = 'relative';
        modalContent.innerHTML = content;
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
        let x1 = parseFloat(this.getAttribute('x1')) || 0;
        let y1 = parseFloat(this.getAttribute('y1')) || 0;
        let x2 = parseFloat(this.getAttribute('x2')) || 0;
        let y2 = parseFloat(this.getAttribute('y2')) || 0;
        let x3 = parseFloat(this.getAttribute('x3')) || 0;
        let y3 = parseFloat(this.getAttribute('y3')) || 0;
        let x4 = parseFloat(this.getAttribute('x4')) || 0;
        let y4 = parseFloat(this.getAttribute('y4')) || 0;
        let color = this.getColorFromType(this.checkType(this.getAttribute('type')));


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
                        style="fill:${color}1c;stroke:black;stroke-width:2; pointer-events: auto;" />
                <circle cx="${x1}" cy="${y1}" r="7" fill="${color}" style="pointer-events: auto;" />
                <circle cx="${x2}" cy="${y2}" r="7" fill="${color}" style="pointer-events: auto;" />
                <circle cx="${x3}" cy="${y3}" r="7" fill="${color}" style="pointer-events: auto;" />
                <circle cx="${x4}" cy="${y4}" r="7" fill="${color}" style="pointer-events: auto;" />
            </svg>
        `;
    }


}

customElements.define('segment-rect', PolygonElement);