# from https://github.com/stanomarochok/nomenclator-cipher-ai/tree/v1 
import cv2
import numpy as np
from ultralytics import YOLO
from typing import List, Optional, Dict
# import pytesseract

# Model paths
MODEL_PATH_PAGE = "detection/components/YOLOv11/best.pt"
MODEL_PATH_WORDS = "detection/words_symbols/YOLOv11/best.pt"


class CipherKeyProcessor:
    def __init__(self):
        """Initialize YOLOv11 models for page segmentation and words/symbols detection."""
        try:
            self.page_model = YOLO(MODEL_PATH_PAGE)
            self.word_model = YOLO(MODEL_PATH_WORDS)
        except Exception as e:
            raise Exception(f"Failed to load models: {e}")

    def preprocess_image(self, image_path: str, binarization_threshold: int = 210, mask_offset: int = 5) -> np.ndarray:
        """Apply pre-processing: binarization and optional contour enhancement."""
        try:
            image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
            if image is None:
                raise ValueError(f"Could not load image: {image_path}")
            _, binary_image = cv2.threshold(image, binarization_threshold, 255, cv2.THRESH_BINARY)
            if mask_offset > 0:
                contours, _ = cv2.findContours(binary_image, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
                cv2.drawContours(binary_image, contours, -1, (255), thickness=mask_offset)
            return binary_image
        except Exception as e:
            raise Exception(f"Pre-processing error: {e}")

    def segment_page(self, image: np.ndarray) -> Optional[np.ndarray]:
        """Perform page segmentation using YOLOv11."""
        try:
            if len(image.shape) == 2:
                image = cv2.cvtColor(image, cv2.COLOR_GRAY2BGR)
            results = self.page_model.predict(image)
            
            # boxes = results[0].boxes
            # line = ""
            # for i in range(len(boxes)):
            #     cls_id = int(boxes.cls[i].item())
            #     conf = boxes.conf[i].item()
            #     x_center, y_center, width, height = boxes.xywh[i].tolist()

            #     # Format as string
            #     line += f"{cls_id} {conf} {x_center} {y_center} {width} {height}"
            # return line
           
            return results[0].boxes.xyxy.cpu().numpy()
        except Exception as e:
            print(f"Segmentation error: {e}")
            return None

    def detect_words_symbols(self, image: np.ndarray) -> Optional[np.ndarray]:
        """Detect words and symbols using YOLOv11."""
        try:
            if len(image.shape) == 2:
                image = cv2.cvtColor(image, cv2.COLOR_GRAY2BGR)
            results = self.word_model.predict(image)
            return results[0].boxes.xyxy.cpu().numpy()
        except Exception as e:
            print(f"Detection error: {e}")
            return None

    def detect_table_structure(self, detections: np.ndarray, row_threshold: int = 50, col_threshold: int = 150) -> List[
        tuple]:
        """Infer table structure from detected words/symbols with configurable thresholds."""
        if detections is None or len(detections) == 0:
            return []
        boxes = detections.tolist()
        tables = []
        for i, box1 in enumerate(boxes):
            for box2 in boxes[i + 1:]:
                if abs(box1[1] - box2[1]) < row_threshold and abs(box1[0] - box2[0]) < col_threshold:
                    tables.append((box1, box2))
        return tables

    def map_plaintext_ciphertext(self, tables: List[tuple]) -> Dict:
        """Map plaintext to ciphertext based on table structure."""
        mapping = {}
        for table in tables:
            mapping[tuple(table[0])] = table[1]
        return mapping

    def htr(self, image: np.ndarray, regions: np.ndarray) -> Dict:
        """Handwritten Text Recognition using Tesseract as a basic implementation."""
        htr_results = {}
        if regions is not None:
            for region in regions:
                x, y, w, h = map(int, region)
                roi = image[y:y + h, x:x + w]
                if len(roi.shape) == 2:
                    roi = cv2.cvtColor(roi, cv2.COLOR_GRAY2BGR)
                text = pytesseract.image_to_string(roi, config='--psm 6')
                htr_results[tuple(region)] = text.strip() or "HTR_failed"
        return htr_results
