## VNDB PHP Client for Laravel

### Configuration

Add the package to project dependencies

`` composer require shikakunhq/vndbclient:dev-master``

Register the provider in ``config/app.php``

`` Shikakunhq\VNDBClient\VNDBServiceProvider::class ``

Pull configuration from vendor. Then config will goes to `` config/vndb.php``

`` php artisan vendor:publish --provider="Shikakunhq\VNDBClient\VNDBServiceProvider" --tag="config" ``




### Basic Usage

##### Retrieving information

```php
use Shikakunhq\VNDBClient\VNDBRequest;
class HomeController {
  public function index() {
    dd(VNDBRequest::getInfobyId(22152));
  }
}
```

Result

```
{array ▼
  +"id": 22152
  +"producer_id": 930
  +"title": "Koi Suru Kokoro to Mahou no Kotoba"
  +"producer": "Hearts"
  +"original": "恋するココロと魔法のコトバ"
  +"aliases": """
    Koikoro\n
    恋コロ\n
    Heart in Love, Word of Magic
    """
  +"released": "2018-05-25"
  +"description": """
    Narumi is a second-year student at Asanoha Gakuen. One day, he headed to the Garden Club room to return something left behind by his classmate Kazane, but he in ▶
    \n
    """
  +"image": "s.vndb.org/cv/32/33932.jpg"
  +"image_nsfw": false
  +"relation": []
  +"characters": array:14 [▼
    0 => array:9 [▼
      "id" => 65569
      "name" => "Haru Haru"
      "original" => "春 ハル"
      "gender" => "f"
      "description" => """
        Race: High level spirit\n
        Breast: Large\n
        \n
        A really energetic high-leveled spirit whose catchphrase is &quot;kiniirimashita~♪.&quot;\n
        \n
        Easily getting impressed. Her tears often coming out even for trivial things. An optimist character who can see a person's good side.
        """
      "bloodt" => null
      "image" => "s.vndb.org/ch/87/79387.jpg"
      "aliases" => "Haruharu"
      "role" => "primary"
    ]
    2 => array:9 [▶]
    4 => array:9 [▶]
    6 => array:9 [▶]
    8 => array:9 [▶]
    10 => array:9 [▶]
    12 => array:9 [▶]
    14 => array:9 [▶]
    16 => array:9 [▶]
    18 => array:9 [▶]
    20 => array:9 [▶]
    22 => array:9 [▶]
    24 => array:9 [▶]
    26 => array:9 [▶]
  ]
}

```

##### Custom Commands

```php
use Shikakunhq\VNDBClient\VNDBRequest;
class HomeController {
  public function index() {
    dd(VNDBRequest::command('get vn basic,details (title="Wagamama High Spec")');
  }
}
```

Visit [API Documentation](https://vndb.org/d11) for more usage



