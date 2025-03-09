from random import randint

class Classifier:
    def __init__(self, min, max):

        self.min = min if min > 0 else int(min*100)
        self.max = max if max > 0 else int(max*100)

    def classify(self, path):
        return randint(self.min, self.max)