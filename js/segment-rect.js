class PolygonElement extends HTMLElement {
    static get observedAttributes() {
        return ['x1', 'y1', 'x2', 'y2', 'x3', 'y3', 'x4', 'y4'];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.draggingPoint = null;
    }

    connectedCallback() {
        this.render();
        this.addEventListeners();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        this.render();
    }

    addEventListeners() {
        this.shadowRoot.addEventListener('mousedown', this.onMouseDown.bind(this));
        this.shadowRoot.addEventListener('mousemove', this.onMouseMove.bind(this));
        this.shadowRoot.addEventListener('mouseup', this.onMouseUp.bind(this));
    }

    onMouseDown(event) {
        const point = event.target;
        if (point.tagName === 'circle') {
            this.draggingPoint = point;
        }
    }

    onMouseMove(event) {
        if (this.draggingPoint) {
            const rect = this.shadowRoot.host.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            this.draggingPoint.setAttribute('cx', x);
            this.draggingPoint.setAttribute('cy', y);
            this.updatePolygonPoints();
        }
    }

    onMouseUp() {
        this.draggingPoint = null;
    }

    updatePolygonPoints() {
        const points = Array.from(this.shadowRoot.querySelectorAll('circle')).map(circle => {
            return `${circle.getAttribute('cx')},${circle.getAttribute('cy')}`;
        }).join(' ');
        this.shadowRoot.querySelector('polygon').setAttribute('points', points);
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

        this.shadowRoot.innerHTML = `
            <svg width="100%" height="100%">
                <polygon points="${x1},${y1} ${x2},${y2} ${x3},${y3} ${x4},${y4}" style="fill:#007bff1c;stroke:black;stroke-width:2" />
                <circle cx="${x1}" cy="${y1}" r="7" fill="#007bff" />
                <circle cx="${x2}" cy="${y2}" r="7" fill="#007bff" />
                <circle cx="${x3}" cy="${y3}" r="7" fill="#007bff" />
                <circle cx="${x4}" cy="${y4}" r="7" fill="#007bff" />
            </svg>
        `;
    }
}

customElements.define('segment-rect', PolygonElement);