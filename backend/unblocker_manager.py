import argparse
import json
import logging
import os
import time

import schedule
import urllib3
from requests import get

urllib3.disable_warnings()

prefix = "apple-auto_"
parser = argparse.ArgumentParser(description="")
parser.add_argument("-api_url", help="API URL", required=True)
parser.add_argument("-api_key", help="API key", required=True)
args = parser.parse_args()
api_url = args.api_url
api_key = args.api_key

logger = logging.getLogger()
logger.setLevel('INFO')
BASIC_FORMAT = "%(asctime)s [%(levelname)s] %(message)s"
DATE_FORMAT = "%d-%m-%Y %H:%M:%S"
formatter = logging.Formatter(BASIC_FORMAT, DATE_FORMAT)
chlr = logging.StreamHandler()
chlr.setFormatter(formatter)
logger.addHandler(chlr)


class API:
    def __init__(self):
        self.url = api_url
        self.key = api_key

    def get_task_list(self):
        try:
            result = json.loads(
                get(f"{self.url}/api/", verify=False, params={"key": self.key, "action": "get_task_list"}).text)
        except Exception as e:
            logger.error("Không lấy được danh sách công việc")
            logger.error(e)
            return False
        else:
            if result['status'] == "fail":
                logger.error("Không lấy được danh sách công việc")
                logger.error(result['message'])
                return False
            elif result['data'] == "":
                return []
            else:
                return result['data'].split(",")


class local_docker:
    def __init__(self, api):
        self.api = api
        self.local_list = self.get_local_list()

    def deploy_docker(self, id):
        logger.info(f"Triển khai vùng chứa{id}")
        os.system(f"docker run -d --name={prefix}{id} \
        -e api_url={self.api.url} \
        -e api_key={self.api.key} \
        -e taskid={id} \
        --restart=on-failure \
        --log-opt max-size=1m --log-opt max-file=2 \
        sahuidhsu/appleid_auto")

    def remove_docker(self, id):
        logger.info(f"Xóa vùng chứa{id}")
        os.system(f"docker stop {prefix}{id} && docker rm {prefix}{id}")

    def get_local_list(self):
        local_list = []
        result = os.popen("docker ps --format \"{{.Names}}\" -a")
        for line in result.readlines():
            if line.find(prefix) != -1:
                local_list.append(line.strip().split("_")[1])
        logger.info(f"Sự hiện diện của địa phương{len(local_list)}hộp đựng")
        return local_list

    def get_remote_list(self):
        result_list = self.api.get_task_list()
        if not result_list:
            logger.info("Không lấy được danh sách tác vụ trên đám mây, hãy sử dụng danh sách cục bộ")
            return self.local_list
        else:
            logger.info(f"Thu được từ đám mây{len(result_list)}Nhiệm vụ")
            return result_list

    def sync(self):
        logger.info("Bắt đầu đồng bộ hóa")
        self.local_list = self.get_local_list()
        # 处理需要删除的容器（本地存在，云端不存在）
        for id in self.local_list:
            if id not in self.get_remote_list():
                self.remove_docker(id)
                self.local_list.remove(id)
        # 处理需要部署的容器（本地不存在，云端存在）
        remote_list = self.get_remote_list()
        for id in remote_list:
            if id not in self.local_list:
                self.deploy_docker(id)
                self.local_list.append(id)
        logger.info("đồng bộ hóa hoàn tất")

    def clean_local_docker(self):
        logger.info("Bắt đầu dọn dẹp các thùng chứa cục bộ")
        self.local_list = self.get_local_list()
        for name in self.local_list:
            self.remove_docker(name)
        logger.info("Dọn dẹp hoàn thành")

    def update(self):
        logger.info("Bắt đầu kiểm tra các bản cập nhật")
        self.local_list = self.get_local_list()
        if len(self.local_list) == 0:
            logger.info("Không cần cập nhật vùng chứa")
            return
        local_list_str = " ".join([f"{prefix}{id}" for id in self.local_list])
        os.system(f"docker run --rm \
        -v /var/run/docker.sock:/var/run/docker.sock \
        containrrr/watchtower \
        --cleanup \
        --run-once \
        {local_list_str}")


def job():
    global Local
    logger.info("Bắt đầu một công việc định kỳ")
    Local.sync()


def update():
    global Local
    logger.info("Bắt đầu nhiệm vụ cập nhật")
    Local.update()


logger.info("Dịch vụ quản lý phụ trợ AppleAuto bắt đầu")
api = API()
Local = local_docker(api)
logger.info("Kéo gương mới nhất")
os.system(f"docker pull sahuidhsu/appleid_auto")
logger.info("Xóa tất cả các vùng chứa cục bộ")
Local.clean_local_docker()
job()
schedule.every(10).minutes.do(job)
schedule.every().day.at("00:00").do(update)
while True:
    schedule.run_pending()
    time.sleep(1)
