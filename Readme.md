# Case Study. Equipment Demand API

## Assumptions,observations and preliminary thinking

1. **observation**: it seems that vans themselves are not too relevant to the task at hand. We are only concerned with "equipment". 
   - Managing vans (their location, availability, distance to travel etc. is a another huge aspect to be implemented)
1. **observation**: the acceptable time quantisation seems to be "daily"
1. **assumption**: it must be a critical issue if an order is placed and there will be no equiment to fulfill it in the starting station or if it will make fulfilling of some previously placed orders impossible. We are likely to need checking that in real time. 
2. **assumption**: The orders are the most numerous objects in the system. Stations, types of equipments are a few dozens to a few hundreds at most. 

## Implementation
### Overall approach and tradeoffs

- completely normalized schema is unfeasible. Complex joins and/or app-side calculation will be slow, hard to maintain and not scalable as volume of order grows. Certain denormalization is needed.
- Therefore, we will creat a so called "counter grid". E.g. for each station and equimpent type, **daily** counters for "on hand" and "booked" number of items will be maintained:
  ![image](https://user-images.githubusercontent.com/21345604/159421802-d84c1f68-a3bc-435b-8e5b-71879d242a8b.png)
- this grid will be automatically "extended" (cron job calling a CLI command) 1 year ahead (configurable)
- as orders are placed, they are first validated if there is enough equipment in the starting station and if any existing future orders will still have enough equipment.
- successfully placing an order "updates" the grid for origin and target stations (can be the same)

### Entities
![image](https://user-images.githubusercontent.com/21345604/159418967-7fcd7c40-0abd-413d-827e-f27d734e0c18.png)

### API


## Todos
- [ ] Check if booked counters are working correctly