PRJ_DIR := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

install:
	docker run --rm -it --tty -v ${PRJ_DIR}:/app composer install

test:
	docker run --rm -it --tty -v ${PRJ_DIR}:/app composer test

clean:
	git clean -fdx -e .idea

terminal:
	docker run --rm -it --tty -v ${PRJ_DIR}:/app composer bash -l

api:
	docker run --rm -it --tty -v ${PRJ_DIR}:/app composer api
