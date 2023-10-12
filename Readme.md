# ReqRes coding task

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

Add next block to the composer.json file:
```json
{
    "repositories": [
        {
          "type": "git",
          "url": "https://github.com/jviapc/reqres-coding-task.git",
          "only": ["jviapc/reqres"]
        }
    ]
}
```

Run the following command
```bash
composer require --dev jviapc/reqres
```

## Testing

Execute the following commands to install dependencies and run unit tests:
```bash
make install
make test
```

Execute the following command if you wish to execute real API calls:
```bash
make api
```
