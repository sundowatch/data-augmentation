import cv2 as cv
import numpy as np
import imutils
import os

path = os.getcwd() + "\\in"

dir_list = os.listdir(path)

i = 0
j = 1
processCount = 9

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

    gaussian2 = cv.GaussianBlur(image, (3,3), cv.BORDER_DEFAULT)

    gaussian_flipX = cv.GaussianBlur(flipX, (5,5), cv.BORDER_DEFAULT)
    gaussian_flipY = cv.GaussianBlur(flipY, (5,5), cv.BORDER_DEFAULT)
    gaussian_flip2 = cv.GaussianBlur(flip2, (5,5), cv.BORDER_DEFAULT)

    gaussian_resized = cv.GaussianBlur(resized, (5,5), cv.BORDER_DEFAULT)

    newFilename = f.split('.')
    newFilename = newFilename[0]

    cv.imwrite('out/'+newFilename+'_original.jpg', image)
    cv.imwrite('out/'+newFilename+'_flipX.jpg', flipX)
    cv.imwrite('out/'+newFilename+'_flipY.jpg', flipY)
    cv.imwrite('out/'+newFilename+'_flip2.jpg', flip2)
    # cv.imwrite('out/'+newFilename+'_rotated_90.jpg', rotated_90)
    # cv.imwrite('out/'+newFilename+'_rotated_180.jpg', rotated_180)
    # cv.imwrite('out/'+newFilename+'_rotated_270.jpg', rotated_270)
    # cv.imwrite('out/'+newFilename+'_resized.jpg', resized)
    cv.imwrite('out/'+newFilename+'_gaussian.jpg', gaussian)
    cv.imwrite('out/'+newFilename+'_gaussian2.jpg', gaussian2)
    # cv.imwrite('out/'+newFilename+'_gaussian_90.jpg', gaussian_90)
    # cv.imwrite('out/'+newFilename+'_gaussian_180.jpg', gaussian_180)
    # cv.imwrite('out/'+newFilename+'_gaussian_270.jpg', gaussian_270)
    cv.imwrite('out/'+newFilename+'_gaussian_flipX.jpg', gaussian_flipX)
    cv.imwrite('out/'+newFilename+'_gaussian_flipY.jpg', gaussian_flipY)
    cv.imwrite('out/'+newFilename+'_gaussian_flip2.jpg', gaussian_flip2)
    # cv.imwrite('out/'+newFilename+'_gaussian_resized.jpg', gaussian_resized)
    
    print(str(j) + "-) " + f + ' is finished!')

    i += processCount
    j += 1
    

print('Bütün resimler kaydedildi')
print(str(j-1) + " tane resimden " + str(i) + 'tane resim üretildi')

