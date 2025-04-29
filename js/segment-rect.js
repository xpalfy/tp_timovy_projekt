class PolygonElement extends HTMLElement {
    static get observedAttributes() {
        return ['x1', 'y1', 'x2', 'y2', 'x3', 'y3', 'x4', 'y4'];
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
    }

    onMouseMove(event) {
        if (this.draggingPoint) {
            const parentRect = this.parentElement.getBoundingClientRect();
            const x = Math.min(Math.max(event.clientX - parentRect.left, 7), parentRect.width - 7);
            const y = Math.min(Math.max(event.clientY - parentRect.top, 7), parentRect.height - 7);

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

            // Compute centroid
            let centroid = {
                x: (A.x + B.x + C.x + D.x) / 4,
                y: (A.y + B.y + C.y + D.y) / 4
            };

            // Sort points clockwise
            pointsArray.sort((a, b) => {
                let angleA = Math.atan2(a.y - centroid.y, a.x - centroid.x);
                let angleB = Math.atan2(b.y - centroid.y, b.x - centroid.x);
                return angleA - angleB;
            });

            // Update circles
            pointsArray.forEach((point, index) => {
                circles[index].setAttribute('cx', point.x);
                circles[index].setAttribute('cy', point.y);
                // Update the attributes too
                this.setAttribute(`x${index + 1}`, point.x);
                this.setAttribute(`y${index + 1}`, point.y);
            });

            // Update polygon
            const newPoints = pointsArray.map(p => `${p.x},${p.y}`).join(' ');
            this.shadowRoot.querySelector('polygon').setAttribute('points', newPoints);
        } else {
            // If no crossing, just update attributes normally
            pointsArray.forEach((point, index) => {
                this.setAttribute(`x${index + 1}`, point.x);
                this.setAttribute(`y${index + 1}`, point.y);
            });
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
                        style="fill:#007bff1c;stroke:black;stroke-width:2; pointer-events: auto;" />
                <circle cx="${x1}" cy="${y1}" r="7" fill="#007bff" style="pointer-events: auto;" />
                <circle cx="${x2}" cy="${y2}" r="7" fill="#007bff" style="pointer-events: auto;" />
                <circle cx="${x3}" cy="${y3}" r="7" fill="#007bff" style="pointer-events: auto;" />
                <circle cx="${x4}" cy="${y4}" r="7" fill="#007bff" style="pointer-events: auto;" />
            </svg>
        `;
    }


}

customElements.define('segment-rect', PolygonElement);