#!/usr/bin/env python
# encoding: utf-8

import requests
import json

import sys

reload(sys)
sys.setdefaultencoding('utf-8')

import MySQLdb

url = "http://www.image.com"
para = {}
header = {}

r = requests.post('http://www.image.com')

r.encoding = 'utf-8'

# print('get result json type', r.text)

# print("get request status code", r.status_code)

# print("get request head", r.headers['Content-Type'])

s = json.loads(r.text)

DB = MySQLdb.connect('localhost', 'root', 'root', 'thinksns_xiaohuangya', charset='utf8')  # OK

cursor = DB.cursor()

for info in s:
    # 先插入套图表，返回套图ID
    try:
        # 执行sql语句
        cursor.execute('insert into ts_pic_object(title,pic_count) values(%s,%d)',
                       (info['title'], info['imgcount']))
        object_id = int(DB.insert_id())
        DB.commit()
    except:  # 如果发生错误则回滚
        DB.rollback()

    print info['images']
    for pic in info['images']:
        # 插入图片表，图片地址，套图id
        cursor.execute('insert into ts_pic(object_id,url) values(%d,%s)',
                       (object_id, info['imgsrc']))
        print(info['title'], pic['imgsrc'])

# 插入完成返回结果并发送请求



# 显示当前系统
# print sys.getdefaultencoding()

# newjson=json.dumps(myjson,ensure_ascii=False)


# d1 = json.loads(r)

# json_r = r.json()
# print(json_r)
