import cv2 as cv
import numpy as np
import imutils
import os

path = "E:\\Programming\\OpenCV\\data-augmentation\\in"

dir_list = os.listdir(path)

i = 0

processCount = 16

for f in dir_list:

    image = cv.imread('in/' + f)

    # FLIPPING
    flipX = cv.flip(image, 0)
    flipY = cv.flip(image, 1)
    flip2 = cv.flip(image, -1)

    # ROTATING
    rotated = []
    for angle in np.arange(90, 360, 90):
        rotated.append(imutils.rotate_bound(image, angle))
    rotated_90 = rotated[0]
    rotated_180 = rotated[1]
    rotated_270 = rotated[2]

    # RESIZING
    width = round(int(image.shape[1]) / 2)
    height = round(int(image.shape[0]) / 2)
    boyutlar = (width, height)

    resized = cv.resize(image, boyutlar)

    # GAUSSIAN BLUR
    gaussian = cv.GaussianBlur(image, (5,5), cv.BORDER_DEFAULT)
    gaussian_90 = cv.GaussianBlur(rotated_90, (5,5), cv.BORDER_DEFAULT)
    gaussian_180 = cv.GaussianBlur(rotated_180, (5,5), cv.BORDER_DEFAULT)
    gaussian_270 = cv.GaussianBlur(rotated_270, (5,5), cv.BORDER_DEFAULT)

    gaussian_flipX = cv.GaussianBlur(flipX, (5,5), cv.BORDER_DEFAULT)
    gaussian_flipY = cv.GaussianBlur(flipY, (5,5), cv.BORDER_DEFAULT)
    gaussian_flip2 = cv.GaussianBlur(flip2, (5,5), cv.BORDER_DEFAULT)

    gaussian_resized = cv.GaussianBlur(resized, (5,5), cv.BORDER_DEFAULT)

    cv.imwrite('out/'+f+'_original.jpg', image)
    cv.imwrite('out/'+f+'_flipX.jpg', flipX)
    cv.imwrite('out/'+f+'_flipY.jpg', flipY)
    cv.imwrite('out/'+f+'_flip2.jpg', flip2)
    cv.imwrite('out/'+f+'_rotated_90.jpg', rotated_90)
    cv.imwrite('out/'+f+'_rotated_180.jpg', rotated_180)
    cv.imwrite('out/'+f+'_rotated_270.jpg', rotated_270)
    cv.imwrite('out/'+f+'_resized.jpg', resized)
    cv.imwrite('out/'+f+'_gaussian.jpg', gaussian)
    cv.imwrite('out/'+f+'_gaussian_90.jpg', gaussian_90)
    cv.imwrite('out/'+f+'_gaussian_180.jpg', gaussian_180)
    cv.imwrite('out/'+f+'_gaussian_270.jpg', gaussian_270)
    cv.imwrite('out/'+f+'_gaussian_flipX.jpg', gaussian_flipX)
    cv.imwrite('out/'+f+'_gaussian_flipY.jpg', gaussian_flipY)
    cv.imwrite('out/'+f+'_gaussian_flip2.jpg', gaussian_flip2)
    cv.imwrite('out/'+f+'_gaussian_resized.jpg', gaussian_resized)
    
    i += processCount
    print(f + ' is finished!')

print('Bütün resimler kaydedildi')
print(str(i) + 'tane resim üretildi')

