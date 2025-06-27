import React from 'react'
import { motion } from 'framer-motion'
import { 
  Lightbulb, 
  Wrench, 
  Users, 
  Award,
  CheckCircle,
  ArrowRight,
  Palette,
  Hammer,
  Calendar,
  Settings,
  Star,
  Zap
} from 'lucide-react'

const Services: React.FC = () => {
  // 主要服务数据
  const mainServices = [
    {
      icon: Lightbulb,
      title: '展台设计',
      description: '创新的设计理念，个性化的展台设计方案，让您的品牌在展会中脱颖而出',
      image: '/images/creative-display-1.jpg',
      features: [
        '创意概念设计',
        '3D效果图制作',
        '展台布局规划',
        '品牌形象设计',
        '材料选择建议',
        '预算控制优化'
      ],
      process: [
        '需求沟通',
        '方案设计',
        '效果图制作',
        '方案确认',
        '施工图纸',
        '材料清单'
      ]
    },
    {
      icon: Wrench,
      title: '展台搭建',
      description: '专业的搭建团队，精湛的施工工艺，确保展台高质量按时完成',
      image: '/images/booth-construction-1.jpg',
      features: [
        '专业施工团队',
        '优质建材选择',
        '精密安装工艺',
        '安全施工保障',
        '进度控制管理',
        '现场质量监控'
      ],
      process: [
        '现场勘察',
        '材料准备',
        '结构搭建',
        '装饰安装',
        '设备调试',
        '完工验收'
      ]
    },
    {
      icon: Users,
      title: '展会策划',
      description: '全方位的展会策划服务，从前期准备到现场执行的完整解决方案',
      image: '/images/exhibition-visitors-1.webp',
      features: [
        '展会策略规划',
        '活动流程设计',
        '人员配置方案',
        '营销推广策略',
        '现场管理服务',
        '效果评估分析'
      ],
      process: [
        '策略制定',
        '方案规划',
        '资源配置',
        '现场执行',
        '活动管理',
        '效果总结'
      ]
    },
    {
      icon: Award,
      title: '设备租赁',
      description: '丰富的设备资源，专业的技术支持，为您的展会提供完善的设备保障',
      image: '/images/tech-display-1.webp',
      features: [
        '音响设备租赁',
        '灯光设备配置',
        '显示设备提供',
        '互动设备支持',
        '技术人员服务',
        '设备维护保障'
      ],
      process: [
        '需求分析',
        '设备选择',
        '方案配置',
        '设备安装',
        '现场支持',
        '设备回收'
      ]
    }
  ]

  // 服务优势
  const advantages = [
    {
      icon: Palette,
      title: '创意设计',
      description: '资深设计师团队，提供原创性设计方案'
    },
    {
      icon: Hammer,
      title: '专业施工',
      description: '经验丰富的施工团队，确保项目高质量完成'
    },
    {
      icon: Calendar,
      title: '按时交付',
      description: '严格的项目管理，保证按时按质交付'
    },
    {
      icon: Settings,
      title: '一站式服务',
      description: '从设计到搭建的完整服务链条'
    },
    {
      icon: Star,
      title: '品质保证',
      description: '严格的质量控制，99%客户满意度'
    },
    {
      icon: Zap,
      title: '快速响应',
      description: '24小时快速响应，及时解决客户需求'
    }
  ]

  // 服务流程
  const serviceProcess = [
    {
      step: '01',
      title: '需求咨询',
      description: '了解客户需求，分析展会目标'
    },
    {
      step: '02',
      title: '方案设计',
      description: '制定设计方案，提供效果图'
    },
    {
      step: '03',
      title: '合同签订',
      description: '确认方案细节，签署合作协议'
    },
    {
      step: '04',
      title: '项目实施',
      description: '专业团队施工，严格质量控制'
    },
    {
      step: '05',
      title: '现场支持',
      description: '展会期间提供现场技术支持'
    },
    {
      step: '06',
      title: '售后服务',
      description: '项目结束后的跟踪服务'
    }
  ]

  return (
    <div className="bg-white">
      {/* 页面头部 */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
          style={{ backgroundImage: 'url(/images/exhibition-booth-1.jpg)' }}
        ></div>
        <div className="relative container mx-auto px-4 text-center">
          <motion.h1 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl lg:text-5xl font-bold mb-6"
          >
            服务项目
          </motion.h1>
          <motion.p 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-blue-100 max-w-3xl mx-auto"
          >
            专业的展览服务，一站式解决方案，为您的展会成功保驾护航
          </motion.p>
        </div>
      </section>

      {/* 主要服务 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="space-y-20">
            {mainServices.map((service, index) => {
              const IconComponent = service.icon
              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 50 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8 }}
                  viewport={{ once: true }}
                  className={`grid lg:grid-cols-2 gap-12 items-center ${
                    index % 2 === 1 ? 'lg:grid-flow-col-dense' : ''
                  }`}
                >
                  <div className={index % 2 === 1 ? 'lg:col-start-2' : ''}>
                    <div className="flex items-center space-x-4 mb-6">
                      <div className="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                        <IconComponent className="w-8 h-8 text-blue-600" />
                      </div>
                      <h2 className="text-3xl lg:text-4xl font-bold text-gray-900">
                        {service.title}
                      </h2>
                    </div>
                    
                    <p className="text-lg text-gray-600 mb-8 leading-relaxed">
                      {service.description}
                    </p>

                    {/* 服务特色 */}
                    <div className="mb-8">
                      <h3 className="text-xl font-bold text-gray-900 mb-4">服务特色</h3>
                      <div className="grid grid-cols-2 gap-3">
                        {service.features.map((feature, featureIndex) => (
                          <div key={featureIndex} className="flex items-center space-x-2">
                            <CheckCircle className="w-5 h-5 text-green-500" />
                            <span className="text-gray-600">{feature}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* 服务流程 */}
                    <div className="mb-8">
                      <h3 className="text-xl font-bold text-gray-900 mb-4">服务流程</h3>
                      <div className="flex flex-wrap gap-2">
                        {service.process.map((step, stepIndex) => (
                          <div key={stepIndex} className="flex items-center">
                            <span className="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
                              {step}
                            </span>
                            {stepIndex < service.process.length - 1 && (
                              <ArrowRight className="w-4 h-4 text-gray-400 mx-2" />
                            )}
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>

                  <div className={index % 2 === 1 ? 'lg:col-start-1' : ''}>
                    <img 
                      src={service.image} 
                      alt={service.title}
                      className="rounded-2xl shadow-2xl w-full"
                    />
                  </div>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>

      {/* 服务优势 */}
      <section className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              服务优势
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              专业团队，优质服务，为您的展会成功提供有力保障
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {advantages.map((advantage, index) => {
              const IconComponent = advantage.icon
              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className="bg-white rounded-xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow duration-300"
                >
                  <div className="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <IconComponent className="w-8 h-8 text-orange-600" />
                  </div>
                  <h3 className="text-xl font-bold text-gray-900 mb-4">{advantage.title}</h3>
                  <p className="text-gray-600">{advantage.description}</p>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>

      {/* 服务流程 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              服务流程
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              标准化的服务流程，确保项目顺利实施
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {serviceProcess.map((process, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="relative bg-white rounded-xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow duration-300"
              >
                <div className="w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-xl font-bold">
                  {process.step}
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-4">{process.title}</h3>
                <p className="text-gray-600">{process.description}</p>
                
                {/* 连接线 */}
                {index < serviceProcess.length - 1 && (
                  <div className="hidden lg:block absolute top-1/2 -right-4 transform -translate-y-1/2">
                    <ArrowRight className="w-8 h-8 text-gray-300" />
                  </div>
                )}
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA区域 */}
      <section className="py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="container mx-auto px-4 text-center">
          <motion.h2 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-3xl lg:text-4xl font-bold mb-6"
          >
            准备开始您的展览项目？
          </motion.h2>
          <motion.p 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            viewport={{ once: true }}
            className="text-xl text-blue-100 mb-8 max-w-2xl mx-auto"
          >
            我们的专业团队将为您提供个性化的展览解决方案，立即联系我们获取免费咨询和报价
          </motion.p>
          
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.4 }}
            viewport={{ once: true }}
            className="flex flex-col sm:flex-row gap-4 justify-center"
          >
            <a 
              href="/contact"
              className="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors duration-200"
            >
              免费咨询 <ArrowRight className="ml-2 w-5 h-5" />
            </a>
            <a 
              href="/cases"
              className="inline-flex items-center justify-center border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold transition-colors duration-200"
            >
              查看案例
            </a>
          </motion.div>
        </div>
      </section>
    </div>
  )
}

export default Services
