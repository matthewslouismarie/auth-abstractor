sudo docker exec -it $1 bash ./check-code-quality.sh
git commit -am "$2"