#!/bin/bash
install_path="/opt/apple_auto"

echo "Một cách hoàn toàn mới để quản lý Apple ID của bạn, quy trình phát hiện và mở khóa Apple ID tự động dựa trên mật khẩu"
echo "Địa chỉ dự án：github.com/Github-Aiko/autoappleid"
echo "Dự án trao đổi nhóm TG: @AikoCute"
echo "Hãy chắc chắn rằng máy đã được cài đặt khi sử dụng Python3.6+ pip3 Docker"
echo "==============================================================="
if python3 -V >/dev/null 2>&1; then
    echo "Python3 đã được cài đặt"
    python_path=$(which python3)
    echo "Đường dẫn Python3: $python_path"
else
    echo "Python3 chưa được cài đặt, đang bắt đầu cài đặt..."
    if [ -f /etc/debian_version ]; then
        apt update && apt -y install python3 python3-pip
    elif [ -f /etc/redhat-release ]; then
        yum -y install python3 python3-pip
    else
       echo "Không thể phát hiện hệ thống hiện tại, đã thoát "
       exit;
    fi
    python_path=$(which python3)
fi
if pip3 >/dev/null 2>&1; then
    echo "đã cài đặt pip3 "
else
    echo "pip3 chưa được cài đặt, đang bắt đầu cài đặt..."
    if [ -f /etc/debian_version ]; then
        apt update && apt -y install python3-pip
    elif [ -f /etc/redhat-release ]; then
        yum -y install python3-pip
    else
       echo "Không thể phát hiện hệ thống hiện tại, đã thoát"
       exit;
    fi
    echo "hoàn tất cài đặt pip3"
fi
if docker >/dev/null 2>&1; then
    echo "Docker đã được cài đặt"
else
    echo "Docker chưa được cài đặt, đang bắt đầu cài đặt..."
    docker version > /dev/null || curl -fsSL get.docker.com | bash
    systemctl enable docker && systemctl restart docker
    echo "Cài đặt Docker hoàn tất"
fi
echo "Bắt đầu cài đặt chương trình phụ trợ Apple_Auto"
echo "Vui lòng nhập API URL（http://xxx.xxx）"
read -e api_url
echo "Vui lòng nhập Khóa API"
read -e api_key
echo "Có nên triển khai bộ chứa Selenium Docker hay không？(y/n)"
read -e run_webdriver
rm -f install_unblocker
if [ "$run_webdriver" = "y" ]; then
    echo "Bắt đầu triển khai bộ chứa Selenium Docker"
    echo "Vui lòng nhập cổng chạy Selenium (mặc định 4444)"
    read -e webdriver_port
    if [ "$webdriver_port" = "" ]; then
        webdriver_port=4444
    fi
    echo "Vui lòng nhập số phiên Selenium tối đa (mặc định là 10)"
    read -e webdriver_max_session
    if [ "$webdriver_max_session" = "" ]; then
        webdriver_max_session=10
    fi
    docker pull selenium/standalone-chrome
    docker run -d --name=webdriver --log-opt max-size=1m --log-opt max-file=1 --shm-size="1g" --restart=always -e SE_NODE_MAX_SESSIONS=$webdriver_max_session -e SE_NODE_OVERRIDE_MAX_SESSIONS=true -e SE_SESSION_RETRY_INTERVAL=1 -e SE_VNC_VIEW_ONLY=1 -p $webdriver_port:4444 -p 5900:5900 selenium/standalone-chrome
    echo "Quá trình triển khai bộ chứa Webdriver Docker đã hoàn tất"
fi
rm -rf install_unblocker
mkdir install_unblocker
cd install_unblocker
echo "Đang bắt đầu tải xuống tệp..."
wget https://raw.githubusercontent.com/Github-Aiko/autoappleid/main/backend/requirements.txt -O requirements.txt
wget https://raw.githubusercontent.com/Github-Aiko/autoappleid/main/backend/unblocker_manager.py -O unblocker_manager.py
SERVICE_FILE="[Unit]
Description=appleauto
Wants=network.target
[Service]
WorkingDirectory=$install_path
ExecStart=$python_path $install_path/unblocker_manager.py -api_url $api_url -api_key $api_key
Restart=on-abnormal
RestartSec=5s
KillMode=mixed
[Install]
WantedBy=multi-user.target"
if [ ! -f "unblocker_manager.py" ];then
    echo "Tệp chương trình chính không tồn tại, vui lòng kiểm tra"
    exit 1
fi
if [ ! -d "$install_path" ]; then
    mkdir "$install_path"
fi
pip3 install -r requirements.txt
cp -f unblocker_manager.py "$install_path"/unblocker_manager.py
if [ ! -f "/usr/lib/systemd/system/appleauto.service" ];then
    rm -rf /usr/lib/systemd/system/appleauto.service
fi
echo -e "${SERVICE_FILE}" > /lib/systemd/system/appleauto.service
systemctl daemon-reload
systemctl enable appleauto
systemctl restart appleauto
systemctl status appleauto
echo "Cài đặt hoàn tất, dịch vụ đã được bật"
echo "Tên dịch vụ mặc định: appleauto"
echo "Cách thao tác:"
echo "Bật dịch vụ: systemctl start appleauto"
echo "Dừng dịch vụ: systemctl stop appleauto"
echo "Khởi động lại dịch vụ: systemctl restart appleauto"
exit 0