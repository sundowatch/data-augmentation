import csv
import os
import cv2 as cv
import numpy as np

def pos(csv_pos, image_dim):
    return int(float(csv_pos) * float(image_dim))



def positions(filename="optikic.csv"):
    ret = []
    pos_olds = ()
    with open('optikic.csv') as csv_file:
        csv_reader = csv.reader(csv_file, delimiter=",")
        line_count = 0
        for row in csv_reader:
            train_type = row[0]
            tr_type = row[0]
            filename = row[1]
            label = row[2]
            lab = row[2]
            p1 = (row[3], row[4])
            p2 = (row[5], row[6])
            p3 = (row[7], row[8])
            p4 = (row[9], row[10])
            pos_old = (p1,p2,p3,p4)
            
            # Filename
            filename = filename.replace('gs://optik-vcm/optikic/','')
            fname = filename.split('-')
            fname = fname[0]
            ext = filename.split('.')
            ext = ext[len(ext) - 1]
            exact_name = fname + '.' + ext

            #path = os.getcwd() + "\\in"
            path = "in/"

            image = cv.imread(path + exact_name)
            
            #size = (int(image.shape[1]), int(image.shape[0]))
            size = (100,100)

            p1 = (pos(p1[0],size[0]), pos(p1[1], size[1]))
            p2 = (pos(p2[0],size[0]), pos(p2[1], size[1]))
            p3 = (pos(p3[0],size[0]), pos(p3[1], size[1]))
            p4 = (pos(p4[0],size[0]), pos(p4[1], size[1]))

            positions = [p1, p2, p3, p4]

            class obj:
                positions = positions
                old_positions = pos_old
                filename = exact_name
                image_size = size
                train_type = tr_type
                label = lab
            
            ret.append(obj)

            line_count += 1
    return ret



def original(obj,output_file_name,form, path_multiply_before='', path_multiply_after=''):
    row_list = []
    for o in obj:
        if form == 'automl':
            old_positions = o.old_positions
            row_list.append([
            o.train_type, 
            path_multiply_before + o.filename, 
            o.label, 
            old_positions[0][0], old_positions[0][1],
            old_positions[1][0], old_positions[1][1],
            old_positions[2][0], old_positions[2][1],
            old_positions[3][0], old_positions[3][1]
            ])
        elif form == 'normal':
            positions = o.positions
            row_list.append([o.train_type, 
            path_multiply_before+ o.filename, 
            o.label, 
            positions[0][0], positions[0][1],
            positions[1][0], positions[1][1],
            positions[2][0], positions[2][1],
            positions[3][0], positions[3][1]
            ])
    with open(output_file_name, 'w', newline='') as file:
        writer = csv.writer(file)
        writer.writerows(row_list)

poses = positions()

original(poses, 'original.csv', 'normal')


