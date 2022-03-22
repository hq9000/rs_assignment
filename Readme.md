# Case Study. Equipment Demand API

## Observations and assumptions

1. **observation**: it seems that vans themselves are not too relevant to the task at hand. We are only concerned with "equipment". 
   - Managing vans (their location, availability, distance to travel etc. is a another significant aspect to be implemented)
1. **observation**: the acceptable time quantisation seems to be "daily"
1. **assumption**: it must be a critical issue if an order is placed and there will be no equipment to fulfill it in the starting station or if it will make fulfilling of some previously placed orders impossible. We are likely to need checking that in real time. 
1. **assumption**: The orders are the most numerous objects in the system. Stations, types of equipments are a few dozens to a few hundreds at most.
1. **observation**: user authentication/authorization is out of scope

## Implementation
### Overall approach and tradeoffs

- completely normalized schema is unfeasible. Complex joins and/or app-side calculation will be slow, hard to maintain and not scalable as volume of order grows. Certain denormalization is needed.
- we will create a so called "counter grid". I.e. for each station and equipment type, **daily** counters for "on hand" and "booked" number of items will be maintained:
  ![image](https://user-images.githubusercontent.com/21345604/159531361-abf47771-d15c-41d2-87b4-cfc3373cea95.png)
- this grid will be automatically "extended" (cron job calling a CLI command) 1 year ahead (configurable)
- as orders are placed, they are first validated if there is enough equipment in the starting station and if any existing future orders will still have enough equipment.
- successfully placing an order "updates" the grid for origin and target stations (can be the same)

### Entities
![image](https://user-images.githubusercontent.com/21345604/159418967-7fcd7c40-0abd-413d-827e-f27d734e0c18.png)

### API

#### Getting equipment usage report

**Request:**
`GET /stations/{station_id}/equipment_usage_report?from=20220322&to=20220323`

- `from` and `to` query parameters can be omitted. If `from` is omitted, current day is assumed. If `to` is omitted, a week starting on the resolved `from` day will be covered.

- if the range is too broad (more than 100 days), the response fails with status code 400.

**Response:**
```json
{
   "Berlin":{
      "Tooth brush":{
         "20220322":{
            "on hand":0,
            "booked":0
         },
         "20220323":{
            "on hand":0,
            "booked":0
         }
      },
      "Towel":{
         "20220322":{
            "on hand":0,
            "booked":0
         },
         "20220323":{
            "on hand":0,
            "booked":0
         }
      }
   }
}
```
#### Creating a new order

`POST /orders`

**body:**
```json
{
    "startStation": 5,
    "endStation": 6,
    "startDayCode": "20220424",
    "endDayCode": "20220426",
    "orderEquipmentCounters": [
        {
            "equipmentType": 5,
            "count": 1
        },
        {
            "equipmentType": 6,
            "count": 1
        }
    ]
}
```

## Possible improvements
- [ ] Use advisory locking to forbid any manipulations with grid when it is being extended to maintain integrity
